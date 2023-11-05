import React from "react"
import ProductionOrderInfo from "./ProductionOrderInfo"
import ProductionOrderItems from "./ProductionOrderItems"
import ProductionOrderSummary from "./ProductionOrderSummary"
import CustomerDropdown from "./CustomerDropdown"
import { observer } from 'mobx-react'
import ErrorBoundary from "../Utils/ErrorBoundary"
import PODropdown from '../Fields/PODropdown'

@observer
export default class ProductionOrder extends React.Component {
	render() {
		var readOnly = this.props.readOnly;
		var customerDropdown = null, lineBreak = null;
		if (!readOnly)
		{
			customerDropdown = <div><span>Customer: </span><CustomerDropdown po={this.props.po} readOnly={readOnly} /></div>;
			lineBreak = <br />;
		}

		return (
			<ErrorBoundary>
				<div className="production-order">
					{customerDropdown}
					{lineBreak}
					<ProductionOrderInfo po={this.props.po} readOnly={readOnly} />
					<br />
					<ProductionOrderItems po={this.props.po} readOnly={readOnly} />
					<br />
					<ProductionOrderSummary po={this.props.po} readOnly={readOnly} />
				</div>
			</ErrorBoundary>
		)
	}
}