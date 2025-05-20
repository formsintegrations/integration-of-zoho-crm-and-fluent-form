import { useState, useEffect } from 'react'
import { Link, Routes, Route, useNavigate, useParams, useLocation } from 'react-router-dom'
import { __ } from '../Utils/i18nwrap'
import zohoCRM from '../resource/img/integ/crm.svg'
import bitsFetch from '../Utils/bitsFetch'
import EditInteg from './AllIntegrations/EditInteg'
import IntegInfo from './AllIntegrations/IntegInfo'
import Log from './AllIntegrations/Log'
import NewInteg from './AllIntegrations/NewInteg'
import ConfirmModal from './Utilities/ConfirmModal'
import SnackMsg from './Utilities/SnackMsg'
import EditIcn from '../Icons/EditIcn'
import TimeIcn from '../Icons/TimeIcn'
import SingleToggle2 from './Utilities/SingleToggle2'

function Integrations() {
  const [integrations, setIntegration] = useState([])
  const [formFields, setformFields] = useState(null)
  const [showMdl, setShowMdl] = useState(false)
  const [confMdl, setconfMdl] = useState({ show: false })
  const [snack, setSnackbar] = useState({ show: false })
  const location = useLocation()
  const navigate = useNavigate()
  const { formID } = useParams()
  const integs = [{ type: 'Zoho CRM', logo: zohoCRM }]
  const [availableIntegs, setAvailableIntegs] = useState(integs)

  const currentURL = location.pathname.replace(/\/(edit|new|info|log)\/.*/, '')

  useEffect(() => {
    bitsFetch({ formId: formID }, 'ff/get/form')
      .then(res => {
        if (res.success) {
          setformFields(res.data?.fields || [])
          setIntegration(res.data?.integrations || [])
        }
      })
      .catch(() => {
        setSnackbar({ show: true, msg: __('Failed to load form data', 'bitffzc') })
      })
  }, [formID])

  const handleStatus = (ev, id) => {
    const tempIntegration = [...integrations]
    const toggleStatus = tempIntegration[id].status == 1 ? 0 : 1
    bitsFetch({ formID, id: tempIntegration[id].id, status: toggleStatus }, 'integration/toggleStatus')
      .then(res => {
        if (res?.success) {
          tempIntegration[id].status = toggleStatus
          setIntegration(tempIntegration)
        }
        if (res?.data) {
          setSnackbar({ show: true, msg: res.data })
        }
      })
      .catch(() => {
        setSnackbar({ show: true, msg: __('Failed to toggle integration status', 'bitffzc') })
      })
  }

  const removeInteg = i => {
    const tempIntegration = { ...integrations[i] }
    const newInteg = [...integrations]
    newInteg.splice(i, 1)
    setIntegration(newInteg)
    bitsFetch({ formId: formID, id: tempIntegration.id }, 'integration/delete').then(response => {
      if (response?.success) {
        setSnackbar({ show: true, msg: `${response.data}` })
      } else {
        newInteg.splice(i, 0, tempIntegration)
        setIntegration([...newInteg])
        setSnackbar({
          show: true,
          msg: `${__('Integration deletion failed. Please try again', 'bitffzc')}`
        })
      }
    })
  }

  const inteDelConf = i => {
    setconfMdl({
      show: true,
      btnTxt: __('Delete', 'bitffzc'),
      btnClass: '',
      body: __('Are you sure to delete this integration?', 'bitffzc'),
      action: () => {
        removeInteg(i)
        closeConfMdl()
      }
    })
  }

  const getLogo = () => <img alt="zohoCRM" loading="lazy" src={zohoCRM} />

  const setNewInteg = type => {
    closeIntegModal()
    navigate(`${currentURL}/new/${type}`)
  }

  const closeIntegModal = () => {
    setShowMdl(false)
    setTimeout(() => setAvailableIntegs(integs), 500)
  }

  const closeConfMdl = () => {
    setconfMdl(prev => ({ ...prev, show: false }))
  }

  return (
    <div className="btcd-s-wrp">
      <SnackMsg snack={snack} setSnackbar={setSnackbar} />
      <ConfirmModal {...confMdl} close={closeConfMdl} />

      <Routes>
        <Route
          path=""
          element={
            <>
              <h2>{__('Integrations', 'bitffzc')}</h2>
              <div className="flx flx-wrp">
                {integrations.length === 0 && (
                  <div
                    role="button"
                    className="btcd-inte-card flx flx-center add-inte mr-4 mt-3"
                    tabIndex="0"
                    onClick={() => setNewInteg('Zoho CRM')}
                    onKeyPress={() => setNewInteg('Zoho CRM')}
                  >
                    <div>+</div>
                  </div>
                )}

                {integrations.map((inte, i) => (
                  <div key={i} className="btcd-inte-card mr-4 mt-3" role="button">
                    <SingleToggle2
                      className="flx mt-2 pos-abs r-n-1 z-9"
                      action={e => handleStatus(e, i)}
                      checked={inte.status == 1}
                    />
                    {getLogo()}
                    <div className="btcd-inte-atn txt-center">
                      <Link
                        to={`${currentURL}/edit/${i}`}
                        className="btn btcd-btn-o-blue btcd-btn-sm mr-2 tooltip pos-rel"
                        style={{ '--tooltip-txt': `'${__('Edit', 'bitffzc')}'` }}
                      >
                        <EditIcn size="15" />
                      </Link>
                      <button
                        className="btn btcd-btn-o-blue btcd-btn-sm mr-2 tooltip pos-rel"
                        style={{ '--tooltip-txt': `'${__('Delete', 'bitffzc')}'` }}
                        onClick={() => inteDelConf(i)}
                      >
                        <span className="btcd-icn icn-trash-2" />
                      </button>
                      <Link
                        to={`${currentURL}/info/${i}`}
                        className="btn btcd-btn-o-blue btcd-btn-sm mr-2 tooltip pos-rel"
                        style={{ '--tooltip-txt': `'${__('Info', 'bitffzc')}'` }}
                      >
                        <span className="btcd-icn icn-information-outline" />
                      </Link>
                      <Link
                        to={`${currentURL}/log/${i}`}
                        className="btn btcd-btn-o-blue btcd-btn-sm tooltip pos-rel"
                        style={{ '--tooltip-txt': `'${__('Log', 'bitffzc')}'` }}
                      >
                        <TimeIcn size="15" />
                      </Link>
                    </div>
                    <div className="txt-center body w-10 py-1" title={`${inte.name} | ${inte.type}`}>
                      <div>{inte.name}</div>
                      <small className="txt-dp">{inte.type}</small>
                    </div>
                  </div>
                ))}
              </div>
            </>
          }
        />

        <Route
          path="new/:integUrlName"
          element={
            <NewInteg
              allIntegURL={currentURL}
              formFields={formFields}
              integrations={integrations}
              setIntegration={setIntegration}
            />
          }
        />
        <Route
          path="edit/:id"
          element={
            <EditInteg
              allIntegURL={currentURL}
              formFields={formFields}
              integrations={integrations}
              setIntegration={setIntegration}
            />
          }
        />
        <Route
          path="info/:id"
          element={<IntegInfo allIntegURL={currentURL} integrations={integrations} />}
        />
        <Route path="log/:id" element={<Log allIntegURL={currentURL} integrations={integrations} />} />
      </Routes>
    </div>
  )
}

export default Integrations
