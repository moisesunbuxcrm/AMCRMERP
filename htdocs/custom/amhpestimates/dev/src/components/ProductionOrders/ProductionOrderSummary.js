import React from "react"
import { observer } from 'mobx-react'
import POText from '../Fields/POText'
import POCheck from '../Fields/POCheck'
import POTextArea from '../Fields/POTextArea'
import PONumber from '../Fields/PONumber'

@observer
export default class ProductionOrderSummary extends React.Component {
  render() {
    var po = this.props.po;
    var readOnly = this.props.readOnly;
    
    return (
      <div>
        <table className="noborder" width="100%">
          <tbody>
            <tr>
              <td align="left" className="potablelabel">Estimate:</td>
              <td align="left" className="potablelabel">Production:</td>
              <td align="left" className="potablelabel">Installation:</td>
            </tr>
            <tr>
              <td align="left"><POTextArea readOnly={readOnly} po={po} prop="ESTOBSERVATION"/></td>
              <td align="left"><POTextArea readOnly={readOnly} po={po} prop="OBSERVATION"/></td>
              <td align="left"><POTextArea readOnly={readOnly} po={po} prop="OBSINST"/></td>
            </tr>
          </tbody>
        </table>
        <table className="noborder" width="100%">
          <tbody>
            <tr className="oddeven">
              <td align="right" className="potablelabel">Order Completed:</td>
              <td align="right" className="potablevalue"><POCheck readOnly={readOnly} po={po} prop="OrderCompleted"/></td>
              <td align="right" className="potablelabel">Installation Time:</td>
              <td align="right" className="potablevalue"><PONumber  readOnly={readOnly} po={po} prop="INSTTIME"/></td>
              <td align="right" className="potablelabel">Total Square Feet:</td>
              <td align="right" className="potablevalue"><PONumber  readOnly="true" po={po} prop="TOTALALUM"/></td>
              <td align="right" className="potablelabel">Total Track feet:</td>
              <td align="right" className="potablevalue"><PONumber  readOnly="true"  po={po} prop="TOTALTRACK"/></td>
              <td align="right" className="potablelabel">Total SQ. Feet:</td>
              <td align="right" className="potablevalue"><PONumber  readOnly="true" po={po} prop="TOTALALUMINST"/></td>
              <td align="right" className="potablelabel"><PONumber  readOnly={readOnly} po={po} prop="YearsWarranty" size="1"/> Years Warranty:</td>
              <td align="right" className="potablevalue"><POCheck readOnly={readOnly} po={po} prop="Check10YearsWarranty"/></td>
            </tr>
            <tr className="oddeven">
              <td align="right" className="potablelabel">Signature Required:</td>
              <td align="right" className="potablevalue"><POCheck readOnly={readOnly} po={po} prop="SignatureReq"/></td>
              <td align="right" className="potablelabel">Customized Value $ (Taxable):</td>
              <td align="right" className="potablevalue"><PONumber  readOnly={readOnly} po={po} prop="CUSTVALUE" precision="2"/></td>
              <td align="right" className="potablelabel">Discount $:</td>
              <td align="right" className="potablevalue"><PONumber  readOnly={readOnly} po={po} prop="Discount" precision="2"/></td>
              <td align="right" className="potablelabel">Total Long feet:</td>
              <td align="right" className="potablevalue"><PONumber  readOnly="true" po={po} prop="TOTALLONG"/></td>
              <td align="right" className="potablelabel">Total Linear FT:</td>
              <td align="right" className="potablevalue"><PONumber  readOnly="true" po={po} prop="TOTALLINEARFT"/></td>
              <td align="right" className="potablelabel">Life Time Warranty:</td>
              <td align="right" className="potablevalue"><POCheck readOnly={readOnly} po={po} prop="LifeTimeWarranty"/></td>
            </tr>
            <tr className="oddeven">
              <td align="right" className="potablelabel">Remove 50 %:</td>
              <td align="right" className="potablevalue"><POCheck readOnly={readOnly} po={po} prop="Check50"/></td>
              <td align="right" className="potablelabel">Other Fees $ (Non taxable):</td>
              <td align="right" className="potablevalue"><PONumber  readOnly={readOnly} po={po} prop="OTHERFEES" precision="2"/></td>
              <td align="right" className="potablelabel">Sales Tax: <PONumber readOnly={readOnly} po={po} prop="SALES_TAX" size="1"/>% </td>
              <td align="right" className="potablevalue"><PONumber  readOnly="true" po={po} prop="SALESTAXAMOUNT" currency={true} precision="2"/></td>
              <td align="right" className="potablelabel">Tapcons:</td>
              <td align="right" className="potablevalue"><PONumber  readOnly="true" po={po} prop="TAPCONS"/></td>
              <td align="right" className="potablelabel">Sqr Feet Inst Price $:</td>
              <td align="right" className="potablevalue"><PONumber  readOnly={readOnly} po={po} prop="SQINSTPRICE" precision="2"/></td>
              <td align="right" className="potablelabel">10 Years maintenance:</td>
              <td align="right" className="potablevalue"><POCheck readOnly={readOnly} po={po} prop="Check10YearsFreeMaintenance"/></td>
            </tr>
            <tr className="oddeven">
              <td align="right" className="potablelabel"></td>
              <td align="right" className="potablevalue"></td>
              <td align="right" className="potablelabel">Permit $ (Non taxable):</td>
              <td align="right" className="potablevalue"><PONumber  readOnly={readOnly} po={po} prop="PERMIT" precision="2"/></td>
              <td align="right" className="potablelabel">Sales Price:</td>
              <td align="right" className="potablevalue"><PONumber  readOnly="true" po={po} prop="SALESPRICE" currency={true} precision="2"/></td>
              <td align="right" className="potablelabel">Fasterners:</td>
              <td align="right" className="potablevalue"><PONumber  readOnly="true" po={po} prop="FASTENERS"/></td>
              <td align="right" className="potablelabel">Total Inst Price:</td>
              <td align="right" className="potablevalue"><PONumber  readOnly="true" po={po} prop="INSTSALESPRICE" currency={true} precision="2"/></td>
              <td align="right" className="potablelabel">Free Opening and Closing:</td>
              <td align="right" className="potablevalue"><POCheck readOnly={readOnly} po={po} prop="CheckFreeOpeningClosing"/></td>
            </tr>
            <tr className="oddeven">
              <td align="right" className="potablelabel"></td>
              <td align="right" className="potablevalue"></td>
              <td align="right" className="potablelabel">Installation $ (Non Taxable):</td>
              <td align="right" className="potablevalue"><PONumber  readOnly={readOnly} po={po} prop="CUSTOMIZE" precision="2"/></td>
              <td align="right" className="potablelabel"></td>
              <td align="right" className="potablevalue"></td>
              <td align="right" className="potablelabel"></td>
              <td align="right" className="potablevalue"></td>
              <td align="right" className="potablelabel"></td>
              <td align="right" className="potablevalue"></td>
              <td align="right" className="potablelabel">No Payments:</td>
              <td align="right" className="potablevalue"><POCheck readOnly={readOnly} po={po} prop="CheckNoPayment"/></td>
            </tr>
          </tbody>
        </table>
      </div>
    )
  }
}