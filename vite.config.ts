import { defineConfig } from 'vite';
import preact from '@preact/preset-vite';
import tailwindcss from '@tailwindcss/vite';
import { resolve } from 'path';

export default defineConfig({
  plugins: [
    preact(),
    tailwindcss(),
  ],
  root: 'frontend',
  build: {
    outDir: resolve(__dirname, 'public'),
    emptyOutDir: false,
    manifest: true,
    rollupOptions: {
      input: resolve(__dirname, 'frontend/src/main.tsx'),
      output: {
        entryFileNames: 'assets/index.js',
        chunkFileNames: 'assets/[name].js',
        assetFileNames: 'assets/[name].[ext]',
      },
    },
  },
  server: {
    proxy: {
      '/api': 'http://localhost:8000',
    },
  },
});
