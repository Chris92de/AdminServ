import { render } from 'preact';
import { App } from './App';
import './index.css';

const container = document.getElementById('app');
if (container) {
  render(<App />, container);
}
