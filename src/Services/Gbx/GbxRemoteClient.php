<?php
declare(strict_types=1);

namespace App\Services;

namespace App\Services\Gbx;

use Exception;
use RuntimeException;
use SimpleXMLElement;

/**
 * Modernized GbxRemote XML-RPC Client for TrackMania Forever & ManiaPlanet dedicated servers.
 */
class GbxRemoteClient
{
    private $socket = null;
    private string $host;
    private int $port;
    private float $timeout;
    private int $handle = 0x80000000;
    private bool $authenticated = false;
    private string $protocol = 'GBXRemote 2';

    public function __construct(string $host = '127.0.0.1', int $port = 5000, float $timeout = 3.0)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function isConnected(): bool
    {
        return is_resource($this->socket) && !feof($this->socket);
    }

    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

    /**
     * Connect to the dedicated server via TCP socket and perform handshake.
     */
    public function connect(): bool
    {
        $errno = 0;
        $errstr = '';
        
        $socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        if (!$socket) {
            throw new RuntimeException("Could not connect to TrackMania server at {$this->host}:{$this->port}: {$errstr} ({$errno})");
        }

        $this->socket = $socket;
        stream_set_timeout($this->socket, (int)$this->timeout, (int)(($this->timeout - (int)$this->timeout) * 1000000));

        // Handshake: read header size & protocol string
        $header = fread($this->socket, 4);
        if (strlen($header) < 4) {
            $this->close();
            throw new RuntimeException("Invalid handshake response from server: empty header.");
        }

        $size = unpack('Vsize', $header)['size'];
        $protocolStr = fread($this->socket, $size);

        if ($protocolStr !== 'GBXRemote 2' && $protocolStr !== 'GBXRemote 1') {
            $this->close();
            throw new RuntimeException("Unsupported server protocol: {$protocolStr}");
        }

        $this->protocol = $protocolStr;
        return true;
    }

    /**
     * Authenticate with the dedicated server.
     */
    public function authenticate(string $login, string $password): bool
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $result = $this->query('Authenticate', $login, $password);
        if ($result === true) {
            $this->authenticated = true;
            return true;
        }

        return false;
    }

    /**
     * Execute an XML-RPC query on the server.
     */
    public function query(string $method, mixed ...$args): mixed
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        $this->handle++;
        if ($this->handle > 0xFFFFFFFF) {
            $this->handle = 0x80000000;
        }

        $xml = $this->buildXmlRpcRequest($method, $args);
        $xmlLen = strlen($xml);

        if ($this->protocol === 'GBXRemote 1') {
            $bytes = pack('Va*', $xmlLen, $xml);
        } else {
            $bytes = pack('VVa*', $xmlLen, $this->handle, $xml);
        }

        $sent = @fwrite($this->socket, $bytes);
        if ($sent === false || $sent === 0) {
            throw new RuntimeException("Failed writing request to server socket.");
        }

        return $this->readResponse($this->handle);
    }

    /**
     * Query with automatic fallback to alternate method name (e.g., Map vs Challenge).
     */
    public function queryCompat(string $primaryMethod, string $fallbackMethod, mixed ...$args): mixed
    {
        try {
            return $this->query($primaryMethod, ...$args);
        } catch (\Throwable $e) {
            return $this->query($fallbackMethod, ...$args);
        }
    }

    /**
     * Read and parse the XML-RPC response for the given request handle.
     */
    private function readResponse(int $expectedHandle): mixed
    {
        do {
            if ($this->protocol === 'GBXRemote 1') {
                $hdrData = fread($this->socket, 4);
                if (strlen($hdrData) < 4) {
                    throw new RuntimeException("Connection closed by server while waiting for response header.");
                }
                $size = unpack('Vsize', $hdrData)['size'];
                $recvHandle = $expectedHandle;
            } else {
                $hdrData = fread($this->socket, 8);
                if (strlen($hdrData) < 8) {
                    throw new RuntimeException("Connection closed by server while waiting for response header.");
                }
                $hdr = unpack('Vsize/Vhandle', $hdrData);
                $size = $hdr['size'];
                $recvHandle = $hdr['handle'];
            }

            if ($size <= 0 || $size > 8 * 1024 * 1024) {
                throw new RuntimeException("Invalid response size ({$size} bytes).");
            }

            $data = '';
            $remaining = $size;
            while ($remaining > 0 && !feof($this->socket)) {
                $chunk = fread($this->socket, min($remaining, 65536));
                if ($chunk === false || $chunk === '') {
                    break;
                }
                $data .= $chunk;
                $remaining -= strlen($chunk);
            }

            if (strlen($data) < $size) {
                throw new RuntimeException("Incomplete response from server (expected {$size} bytes, got " . strlen($data) . ").");
            }

            // If callback packet (not our request handle), continue waiting for our response
        } while ($this->protocol !== 'GBXRemote 1' && (int)$recvHandle !== (int)$expectedHandle);

        return $this->parseXmlRpcResponse($data);
    }

    /**
     * Build an XML-RPC request payload.
     */
    private function buildXmlRpcRequest(string $method, array $params): string
    {
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
        $xml .= "<methodCall>\n";
        $xml .= "<methodName>" . htmlspecialchars($method, ENT_XML1, 'UTF-8') . "</methodName>\n";
        $xml .= "<params>\n";
        foreach ($params as $param) {
            $xml .= "<param>" . $this->serializeValue($param) . "</param>\n";
        }
        $xml .= "</params>\n";
        $xml .= "</methodCall>";
        return $xml;
    }

    /**
     * Serialize a PHP value into an XML-RPC value node.
     */
    private function serializeValue(mixed $val): string
    {
        if (is_int($val)) {
            return "<value><int>{$val}</int></value>";
        }
        if (is_bool($val)) {
            $b = $val ? '1' : '0';
            return "<value><boolean>{$b}</boolean></value>";
        }
        if (is_float($val)) {
            return "<value><double>{$val}</double></value>";
        }
        if (is_string($val)) {
            return "<value><string>" . htmlspecialchars($val, ENT_XML1, 'UTF-8') . "</string></value>";
        }
        if (is_array($val)) {
            $isAssoc = array_keys($val) !== range(0, count($val) - 1);
            if ($isAssoc) {
                $out = "<value><struct>\n";
                foreach ($val as $k => $v) {
                    $out .= "<member><name>" . htmlspecialchars((string)$k, ENT_XML1, 'UTF-8') . "</name>" . $this->serializeValue($v) . "</member>\n";
                }
                $out .= "</struct></value>";
                return $out;
            } else {
                $out = "<value><array><data>\n";
                foreach ($val as $item) {
                    $out .= $this->serializeValue($item) . "\n";
                }
                $out .= "</data></array></value>";
                return $out;
            }
        }
        return "<value><string>" . htmlspecialchars((string)$val, ENT_XML1, 'UTF-8') . "</string></value>";
    }

    /**
     * Parse XML-RPC response into native PHP types.
     */
    private function parseXmlRpcResponse(string $xmlContent): mixed
    {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOBLANKS);
        if (!$doc) {
            throw new RuntimeException("Failed to parse XML-RPC response XML: {$xmlContent}");
        }

        if (isset($doc->fault)) {
            $faultVal = $this->parseXmlValue($doc->fault->value);
            $code = $faultVal['faultCode'] ?? 0;
            $msg = $faultVal['faultString'] ?? 'Unknown XML-RPC Fault';
            throw new RuntimeException("XML-RPC Fault [{$code}]: {$msg}", (int)$code);
        }

        if (isset($doc->params->param->value)) {
            return $this->parseXmlValue($doc->params->param->value);
        }

        return null;
    }

    private function parseXmlValue(\SimpleXMLElement $node): mixed
    {
        if (isset($node->string)) {
            return (string)$node->string;
        }
        if (isset($node->int) || isset($node->i4)) {
            return (int)($node->int ?? $node->i4);
        }
        if (isset($node->boolean)) {
            return ((string)$node->boolean === '1' || (string)$node->boolean === 'true');
        }
        if (isset($node->double)) {
            return (float)$node->double;
        }
        if (isset($node->struct)) {
            $arr = [];
            foreach ($node->struct->member as $member) {
                $key = (string)$member->name;
                $arr[$key] = $this->parseXmlValue($member->value);
            }
            return $arr;
        }
        if (isset($node->array)) {
            $arr = [];
            if (isset($node->array->data->value)) {
                foreach ($node->array->data->value as $val) {
                    $arr[] = $this->parseXmlValue($val);
                }
            }
            return $arr;
        }

        // Default to text content
        return (string)$node;
    }

    public function close(): void
    {
        if (is_resource($this->socket)) {
            @fclose($this->socket);
        }
        $this->socket = null;
        $this->authenticated = false;
    }
}
