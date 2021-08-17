/* eslint-disable no-undef */
/* eslint-disable react/jsx-props-no-spreading */
/* eslint-disable no-console */
/* eslint-disable react/jsx-one-expression-per-line */

import { lazy, Suspense } from 'react'
import { BrowserRouter as Router, Switch, Route, NavLink, Link } from 'react-router-dom'
import './resource/sass/app.scss'
// eslint-disable-next-line import/no-extraneous-dependencies
import { __ } from './Utils/i18nwrap'
import './resource/icons/style.css'
import Loader from './components/Loaders/Loader'
import logo from './resource/img/integ/crm.svg'
import Integrations from "./components/Integrations"
import TableLoader from './components/Loaders/TableLoader'
import formLogo from '../bit-form.png'

const AllForms = lazy(() => import('./pages/AllForms'))
const Error404 = lazy(() => import('./pages/Error404'))

function App() {  
  const loaderStyle = { height: '90vh' }

  return (
    <Suspense fallback={(<Loader className="g-c" style={loaderStyle} />)}>
      <Router basename={typeof bitffzc !== 'undefined' ? bitffzc.baseURL : '/'}>
        <div className="Btcd-App">

          <div className="nav-wrp">
            <div className="flx">
              <div className="logo flx" title={__('Integrations for Fluent Form', 'bitffzc')}>
                <Link to="/" className="flx">
                  <img src={logo} alt="logo" className="ml-2" />
                  <span className="ml-2">Integrations for Fluent Form</span>
                </Link>
              </div>
              <nav className="top-nav ml-2">
                <NavLink
                  exact
                  to="/"
                  activeClassName="app-link-active"
                >
                  {__('My Forms', 'bitffzc')}
                </NavLink>
              </nav>
              <div className="flx" title={`Bit Form – WordPress Drag & Drop Contact Form, Payment Form Builder`} style={{flexFlow:'row-reverse', flexGrow:3}}>
                <a href="https://wordpress.org/plugins/bit-form" className="flx white">
                  <span className="ml-2">Powered By Bit Form</span>
                  <img src={formLogo} alt="logo" className="ml-2" height='25px'/>
                </a>
              </div>
            </div>
          </div>

          <div className="route-wrp">
            <Switch>
              <Route exact path="/">
                <Suspense fallback={<TableLoader />}>
                  <AllForms/>
                </Suspense>
              </Route>
              <Route path="/form/:formID/integrations">
                <Suspense fallback={<Loader className="g-c" style={loaderStyle} />}>
                  <Integrations/>
                </Suspense>
              </Route>
              <Route path="*">
                <Error404 />
              </Route>
            </Switch>
          </div>
        </div>
        </Router>
    </Suspense>
  )
}

export default App
