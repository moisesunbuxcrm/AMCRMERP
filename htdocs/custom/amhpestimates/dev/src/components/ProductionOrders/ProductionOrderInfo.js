import React from "react"
import { observer } from 'mobx-react'
import POText from '../Fields/POText'
import POPhone from '../Fields/POPhone'
import PODropdown from '../Fields/PODropdown'
import PONumber from '../Fields/PONumber'
import PODate from '../Fields/PODate'

@observer
export default class ProductionOrderInfo extends React.Component {
  render() {
    var po = this.props.po;
		var readOnly = this.props.readOnly;
 
    return (
        <div>
          <table className="noborder" width="100%">
            <tbody>
              <tr className="oddeven">
                <td align="right" className="potablelabel">Number:</td>
                <td align="left" className="potablevalue"><POText po={po} prop="PONUMBER" readOnly={readOnly}/></td>
                <td align="right" className="potablelabel">Name:</td>
                <td align="left" className="potablevalue"><POText po={po} prop="CUSTOMERNAME" readOnly={true} linkTo={"../../societe/card.php?socid="+po.customerId}/></td>
                <td align="right" className="potablelabel">City:</td>
                <td align="left" className="potablevalue"><POText po={po} prop="CITY" readOnly={true}/></td>
                <td align="right" className="potablelabel">ST:</td>
                <td align="left" className="potablevalue"><POText po={po} prop="STATE" readOnly={true}/></td>
                <td align="right" className="potablelabel">Phone Number:</td>
                <td align="left" className="potablevalue"><POPhone po={po} prop="PHONENUMBER1" readOnly={true}/></td>
                <td align="right" className="potablelabel">ESTIMATE + HT:</td>
                <td align="left" className="potablevalue"><PONumber po={po} prop="ESTHTVALUE" readOnly={readOnly}/></td>
              </tr>
              <tr className="oddeven">
                <td align="right" className="potablelabel">Quote Date:</td>
                <td align="left" className="potablevalue potablevalue-date"><PODate po={po} prop="QUOTEDATE" readOnly={readOnly}/></td>
                <td align="right" className="potablelabel">Address:</td>
                <td align="left" className="potablevalue" colSpan="3"><POText po={po} prop="CUSTOMERADDRESS" readOnly={true}/></td>
                <td align="right" className="potablelabel">Zip Code:</td>
                <td align="left" className="potablevalue"><POText po={po} prop="ZIPCODE" readOnly={true}/></td>
                <td align="right" className="potablelabel">Mobile Number:</td>
                <td align="left" className="potablevalue"><POPhone po={po} prop="PHONENUMBER2" readOnly={true}/></td>
                <td align="right" className="potablelabel">PO + HT:</td>
                <td align="left" className="potablevalue"><PONumber po={po} prop="HTVALUE" readOnly={readOnly}/></td>
              </tr>
              <tr className="oddeven">
                <td align="right" className="potablelabel">PO Date:</td>
                <td align="left" className="potablevalue potablevalue-date"><PODate po={po} prop="PODATE" readOnly={readOnly}/></td>
                <td align="right" className="potablelabel">E-Mail:</td>
                <td align="left" className="potablevalue" colSpan="5"><POText po={po} prop="EMail" readOnly={true}/></td>
                <td align="right" className="potablelabel">Fax:</td>
                <td align="left" className="potablevalue"><POPhone po={po} prop="FAXNUMBER" readOnly={true}/></td>
                <td align="right" className="potablelabel">Color:</td>
                <td align="left" className="potablevalue"><PODropdown po={po} prop="COLOR" items={estimateData.colors} readOnly={readOnly}/></td>
              </tr>
              <tr className="oddeven">
                <td align="right" className="potablelabel">Vendor:</td>
                <td align="left" className="potablevalue"><POText po={po} prop="Salesman" readOnly={readOnly}/></td>
                <td align="right" className="potablelabel">Contact:</td>
                <td align="left" className="potablevalue" colSpan="3"><POText po={po} prop="CONTACTNAME" readOnly={true}/></td>
                <td align="right" className="potablelabel">Contact Phone:</td>
                <td align="left" className="potablevalue"><POText po={po} prop="CONTACTPHONE1" readOnly={true}/></td>
                <td align="right" className="potablelabel">Contact Mobile:</td>
                <td align="left" className="potablevalue"><POText po={po} prop="CONTACTPHONE2" readOnly={true}/></td>
                <td align="right" className="potablelabel">Sq. Feet Price:</td>
                <td align="left" className="potablevalue"><PONumber po={po} prop="SQFEETPRICE" readOnly={readOnly} precision="2"/></td>
              </tr>
            </tbody>
          </table>
        </div>
      )
  }
}