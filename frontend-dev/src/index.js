import { lazy, Suspense } from 'react'
import ReactDOM from 'react-dom'
import { AllFormContextProvider } from './Utils/AllFormContext'
import Loader from './components/Loaders/Loader'

const App = lazy(() => import('./App'))

if (typeof bitffzc !== 'undefined' && bitffzc.assetsURL !== undefined) {
  // eslint-disable-next-line camelcase
  __webpack_public_path__ = `${bitffzc.assetsURL}/js/`
}
if (typeof bitffzc !== 'undefined' && bitffzc.baseURL && `${window.location.pathname + window.location.search}#` !== bitffzc.baseURL) {
  bitffzc.baseURL = `${window.location.pathname + window.location.search}#`
}
if (window.location.hash === '') {
  window.location = `${window.location.href}#/`
}
if ('serviceWorker' in navigator && process.env.NODE_ENV === 'production') {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register(`${__webpack_public_path__}service-worker.js`).then(registration => {
      // eslint-disable-next-line no-console
      console.log('SW registered: ', registration)
    }).catch(registrationError => {
      // eslint-disable-next-line no-console
      console.log('SW registration failed: ', registrationError)
    })
  })
} else {
  // eslint-disable-next-line no-console
  console.log('no sw')
}

ReactDOM.render(
  <AllFormContextProvider>
      <Suspense fallback={(
        <Loader style={{
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center',
          height: '90vh',
        }}
        />
      )}
      >
        <App />
      </Suspense>
  </AllFormContextProvider>, document.getElementById('btcd-app'),
)

// serviceWorker.register();
