import { h } from 'preact';

interface TmTextProps {
  html?: string;
  raw?: string;
  fallback?: string;
  className?: string;
}

export function TmText({ html, raw, fallback = '', className = '' }: TmTextProps) {
  if (html) {
    return (
      <span
        className={`tm-text ${className}`}
        dangerouslySetInnerHTML={{ __html: html }}
      />
    );
  }
  return <span className={`tm-text ${className}`}>{raw || fallback}</span>;
}
