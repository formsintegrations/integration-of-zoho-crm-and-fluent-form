/* eslint-disable no-undef */
/* eslint-disable react/jsx-props-no-spreading */
/* eslint-disable no-console */
/* eslint-disable react/jsx-one-expression-per-line */

import { lazy, Suspense } from 'react'
import { HashRouter as Router, Routes, Route, NavLink, Link } from 'react-router-dom'
import './resource/sass/app.scss'
import { __ } from './Utils/i18nwrap'
import './resource/icons/style.css'
import Loader from './components/Loaders/Loader'
import logo from './resource/img/integ/crm.svg'
import Integrations from './components/Integrations'
import TableLoader from './components/Loaders/TableLoader'
import Settings from './pages/Settings'

const AllForms = lazy(() => import('./pages/AllForms'))
const Error404 = lazy(() => import('./pages/Error404'))

const App = () => {
  const loaderStyle = { height: '90vh' }

  return (
    <Router>
      <div className="Btcd-App">
        {/* Top Navigation */}
        <div className="nav-wrp">
          <div className="flx">
            <div className="logo flx" title={__('Integrations for Fluent Form', 'bitffzc')}>
              <Link to="/" className="flx">
                <img src={logo} alt="logo" className="ml-2" />
                <span className="ml-2">Integrations for Fluent Form</span>
              </Link>
            </div>
            <nav className="top-nav ml-2">
              <NavLink to="/" className={({ isActive }) => (isActive ? 'app-link-active' : '')}>
                {__('My Forms', 'bitffzc')}
              </NavLink>
              <NavLink to="/settings" className={({ isActive }) => (isActive ? 'app-link-active' : '')}>
                {__('Settings', 'bitffzc')}
              </NavLink>
            </nav>
          </div>
        </div>

        <div className="route-wrp">
          <Routes>
            <Route
              path="/"
              element={
                <Suspense fallback={<TableLoader />}>
                  <AllForms />
                </Suspense>
              }
            />
            <Route
              path="/form/:formID/integrations/*"
              element={
                <Suspense fallback={<TableLoader />}>
                  <Integrations />
                </Suspense>
              }
            />
            <Route
              path="/settings"
              element={
                <Suspense fallback={<TableLoader />}>
                  <Settings />
                </Suspense>
              }
            />
            <Route
              path="*"
              element={
                <Suspense fallback={<TableLoader />}>
                  <Error404 />
                </Suspense>
              }
            />
          </Routes>
        </div>
      </div>
    </Router>
  )
}

export default App
