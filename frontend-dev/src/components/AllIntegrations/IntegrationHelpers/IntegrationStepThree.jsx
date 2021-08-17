import { __ } from '../../../Utils/i18nwrap'

export default function IntegrationStepThree({ step, saveConfig, edit, disabled }) {
  return (
    edit
      ? (
        <div className="txt-center w-9 mt-3">
          <button onClick={saveConfig} className="btn btcd-btn-lg green sh-sm flx" type="button" disabled={disabled}>
            {__('Update', 'bitffzc')}
          </button>
        </div>
      )
      : (
        <div className="btcd-stp-page txt-center" style={{ width: step === 3 && '90%', height: step === 3 && '100%' }}>
          <h2 className="ml-3">{__('Successfully Integrated', 'bitffzc')}</h2>
          <button onClick={saveConfig} className="btn btcd-btn-lg green sh-sm" type="button">
            {__('Finish & Save ', 'bitffzc')}
            ✔
          </button>
        </div>
      )
  )
}
