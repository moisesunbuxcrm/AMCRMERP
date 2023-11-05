import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom';
import Routes from './routes';

import store from './store/store'
import { Provider } from 'react-redux'

var appElement = document.getElementById('root');

ReactDOM.render(
  <React.StrictMode>
    <Provider store={store}>
      <BrowserRouter basename={`${process.env.REACT_APP_ROUTES_URL}`}>
        <Routes />
      </BrowserRouter>
    </Provider>
  </React.StrictMode>,
  appElement
);
