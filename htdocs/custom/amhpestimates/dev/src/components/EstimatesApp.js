import React from "react"
import { action, extendObservable } from 'mobx';
import { observer } from 'mobx-react'
import ProductionOrder from "./ProductionOrders/ProductionOrder";
import ErrorBoundary from "./Utils/ErrorBoundary";
import SaveButton from "./Utils/SaveButton";
import CancelButton from "./Utils/CancelButton";
import DeleteButton from "./Utils/DeleteButton";
import DevTools from 'mobx-react-devtools';
import { STORE_STATES } from '../stores/StoreBase';

@observer
export default class EstimatesApp extends React.Component {
	isReady;
	isError;
	isBusy;
	errorMsg;

	constructor() {
		super();
		extendObservable(this, {
			readOnly: null
		});
	}

	render() {
		var store = this.props.store;
		var backToPage = this.props.backToPage;

		this.init(this.readOnly == null);

		if (!backToPage)
			backToPage = "list.php?mainmenu=amhpestimates&restore_lastsearch_values=1";

		this.isBusy = !this.isReady && !this.isError;
		var po = this.isReady ? store.CurrentProductionOrder : null;
		var showProgress = store.progressTracker && store.progressTracker.max > 0;
		var progressInfo = showProgress && this.createProgressBar();

		var currentPO = null;
		if (this.isError)
			currentPO = <h1>Error: {this.errorMsg}</h1>;
		else if (this.isBusy || showProgress)
		{
			if (store.statusDescription)
				this.progressDescription = store.statusDescription;
			currentPO = <h1>Busy {this.progressDescription}{progressInfo?progressInfo.progressText:""}...</h1>;
		}
		else if (this.isReady)
		{
			if (po == null)
			{
				currentPO = <h1>No Production Orders Found</h1>;
				if (store.Modified)
					currentPO = <h1>Please save changes to view more Production Orders or click "Back to list" above to load more Production Orders.</h1>;
				else if (store.productionOrders != null && store.productionOrders.length>0)
					currentPO = <h1>Please create a new Production Order below or click "Back to list" above to load more Production Orders.</h1>;
			}
			else
				currentPO = <ProductionOrder po={po} readOnly={this.readOnly} />;
		}

		var footerButtons = null;
		var buttonsVisibility = null;
		if (this.isBusy || showProgress || this.isError)
			buttonsVisibility = { display: "none" };
		else{
			footerButtons = this.buildFooterButtons(po);
		}

		if (currentPO == null)
			currentPO = "Boom!";

		var rememberToSaveMessage=null;
		if (store.Modified)
			rememberToSaveMessage = <li className="noborder litext"><span style={{color: "red", fontWeight: "bold"}}>REMEMBER TO SAVE!</span></li>;

		return (
			<div>
				<div className="fiche">
					<div>
						<table summary="" className="centpercent notopnoleftnoright" style={{ marginBottom: "2px" }}>
							<tbody>
								<tr>
									<td className="nobordernopadding widthpictotitle" valign="middle">
										<img src="../../theme/eldy/img/title_setup.png" alt="" title="" className="valignmiddle" id="pictotitle" />
									</td>
									<td className="nobordernopadding" valign="middle">
										<div className="titre">Estimate Browser</div>
									</td>
									<td className="nobordernopadding" valign="middle">
										<div className="estimates-pagination">						
											<div className="pagination paginationref">
												<ul className="right">
													{rememberToSaveMessage}
													<li className="noborder litext"><a href="../../admin/const.php" target="_blank">Edit constants</a></li>
													<li className="noborder litext"><a href={backToPage}>Back to list</a></li>
													<li className="pagination"><a accessKey="p" onClick={this.prevPO.bind(this)}><i className="fa fa-chevron-left"></i></a></li>
													<li className="pagination"><a accessKey="n" onClick={this.nextPO.bind(this)}><i className="fa fa-chevron-right"></i></a></li>
												</ul>
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<br />
					{currentPO}
					<hr style={buttonsVisibility}/>
					{footerButtons}
				</div>
				<div className="estimates-progress" style={{ display: (showProgress ? 'block' : 'none') }}>
					<div className="estimates-progress-bar" style={progressInfo?progressInfo.progressStyle:null}></div>
				</div>
			</div>
		)
	}

	@action setReadOnly(readOnly)
	{
		this.readOnly = readOnly;
	}

	nextPO() {
		this.props.store.fetchNextProductionOrder().then(
			action("nextPO", () => {
				var po = this.props.store.CurrentProductionOrder;
				history.pushState(po.POID, po.PONUMBER, "card.php?poid="+po.POID);
				var allowEdit = po != null && po.Modified || this.props.action == 'edit';
				this.setReadOnly(!allowEdit);
			})
		)
	}

	prevPO() {
		this.props.store.fetchPrevProductionOrder().then(
			action("prevPO", () => {
				var po = this.props.store.CurrentProductionOrder;
				history.pushState(po.POID, po.PONUMBER, "card.php?poid="+po.POID);
				var allowEdit = po != null && po.Modified || this.props.action == 'edit';
				this.setReadOnly(!allowEdit);
			})
		)
	}

	newPO() {
		this.props.store.createNewProductionOrder(this.props.custId);
		this.setReadOnly(false);
	}

	delPO() {
		if (window.confirm("Are you sure you want to DELETE this estimate??"))
		{
			this.props.store.deleteCurrentProductionOrder();
			var po = this.props.store.CurrentProductionOrder;
			history.pushState(po.POID, po.PONUMBER, "card.php?poid="+po.POID);
			var allowEdit = po != null && po.Modified || this.props.action == 'edit';
			this.setReadOnly(!allowEdit);
		}
	}

	editPO() {
		this.setReadOnly(false);
	}

	/** Sets up the component for the first render. Fetches first PO */
	init(firstRender)
	{
		this.isError = false;

		var store = this.props.store;
		var custid = this.props.custId;
		var readOnly = true;
		var poid = this.props.initialPOID;
		var action = this.props.initialAction;
		if (firstRender)
		{
			if (poid)
			{
				store.fetchProductionOrder(poid, custid);
				readOnly = action != "edit";
			}
			else
			{
				this.newPO();
				readOnly = false;
			}
			this.setReadOnly(readOnly);
		}

		if (!this.isError)
		{
			this.isReady = store.getStatus() == STORE_STATES.READY;
			this.isError = store.getStatus() == STORE_STATES.ERROR;
			this.errorMsg = this.isError && store.statusDescription;
		}
	}

	/** Creates toolbar of buttons for bottom of page */
	buildFooterButtons(po)
	{
		var store = this.props.store;
		var custid = this.props.custId;
		var modifyButton=null;
		if (this.readOnly && !po.invoiceLocked && po != null)
			modifyButton = <div className="inline-block divButAction"><a className="butAction" onClick={this.editPO.bind(this)}>Modify</a></div>;
		else
			modifyButton = <div className="inline-block divButAction"><a className="butActionRefused" onClick={this.editPO.bind(this)}>Modify</a></div>;

		var duplicateEstimateButton=null;
		if (this.readOnly && po != null)
			duplicateEstimateButton = <div className="inline-block divButAction"><a className="butAction" onClick={this.copyPO.bind(this)}>Copy</a></div>;
		else
			duplicateEstimateButton = <div className="inline-block divButAction"><a className="butActionRefused" onClick={this.copyPO.bind(this)}>Copy</a></div>;
	
		var printEstimateButton=null;
		if (this.readOnly && po != null)
			printEstimateButton = <div className="inline-block divButAction"><a className="butAction" onClick={this.printEstimate.bind(this)}>Print Estimate</a></div>;
		else
			printEstimateButton = <div className="inline-block divButAction"><a className="butActionRefused" onClick={this.printEstimate.bind(this)}>Print Estimate</a></div>;

		var printInvoiceButton=null;
		if (this.readOnly && po != null)
			printInvoiceButton = <div className="inline-block divButAction"><a className="butAction" onClick={this.printInvoice.bind(this)}>Print Invoice</a></div>;
		else
			printInvoiceButton = <div className="inline-block divButAction"><a className="butActionRefused" onClick={this.printInvoice.bind(this)}>Print Invoice</a></div>;

		var createInvoiceButton=null;
		if (estimateData.user.admin)
		{
			var createInvoiceTitle = "Create Invoice";
			if (po.invoiceId > 0)
				createInvoiceTitle = "Replace Invoice";
			if (this.readOnly && po != null && !po.invoiceLocked)
				createInvoiceButton = <div className="inline-block divButAction"><a className="butAction" onClick={this.createInvoice.bind(this)}>{createInvoiceTitle}</a></div>;
			else
				createInvoiceButton = <div className="inline-block divButAction"><a className="butActionRefused" onClick={this.createInvoice.bind(this)}>{createInvoiceTitle}</a></div>;
		}

		var permitButton=null;
		//if (estimateData.user.admin) 
		{
			var permitButtonTitle = "Create Permit";
			var permitButtonAction = this.createPermit.bind(this);
			if (po.permitId > 0)
			{
				permitButtonTitle = "Show Permit";
				permitButtonAction = this.gotoPermit.bind(this);
			}
			if (this.readOnly && po != null)
				permitButton = <div className="inline-block divButAction"><a className="butAction" onClick={permitButtonAction}>{permitButtonTitle}</a></div>;
			else
				permitButton  = <div className="inline-block divButAction"><a className="butActionRefused" onClick={permitButtonAction}>{permitButtonTitle}</a></div>;
		}

		var showInvoiceButton=null;
		if (estimateData.user.admin)
		{
			if (po.invoiceId > 0)
				showInvoiceButton = <div className="inline-block divButAction"><a className="butAction" onClick={this.showInvoice.bind(this)}>View Invoice</a></div>;
			else
				showInvoiceButton = <div className="inline-block divButAction"><a className="butActionRefused" onClick={this.showInvoice.bind(this)}>View Invoice</a></div>;
		}

		var printContractButton=null;
		if (this.readOnly && po != null)
			printContractButton = <div className="inline-block divButAction"><a className="butAction" onClick={this.printContract.bind(this)}>Print Contract</a></div>;
		else
			printContractButton = <div className="inline-block divButAction"><a className="butActionRefused" onClick={this.printContract.bind(this)}>Print Contract</a></div>;
	
		var printInstOrderButton=null;
		if (this.readOnly && po != null)
			printInstOrderButton = <div className="inline-block divButAction"><a className="butAction" onClick={this.printInstOrder.bind(this)}>Print Inst. Order</a></div>;
		else
			printInstOrderButton = <div className="inline-block divButAction"><a className="butActionRefused" onClick={this.printInstOrder.bind(this)}>Print Inst. Order</a></div>;
	
		var printProdOrderButton=null;
		if (this.readOnly && po != null)
			printProdOrderButton = <div className="inline-block divButAction"><a className="butAction" onClick={this.printProdOrder.bind(this)}>Print Prod. Order</a></div>;
		else
			printProdOrderButton = <div className="inline-block divButAction"><a className="butActionRefused" onClick={this.printProdOrder.bind(this)}>Print Prod. Order</a></div>;
	
		var footerButtons = null;
		footerButtons = 
		(<div align="right">
				{printEstimateButton}
				{printInvoiceButton}
				{createInvoiceButton}
				{showInvoiceButton}
				{printContractButton}
				{permitButton}
				{printInstOrderButton}
				{printProdOrderButton}
				<SaveButton store={store} onSave={this.onSave.bind(this)} />
				<CancelButton store={store} readOnly={this.readOnly} onClick={this.onCancel.bind(this)} />
				{modifyButton}
				<DeleteButton store={store} onClick={this.delPO.bind(this)}/>
				{duplicateEstimateButton}
				<div className="inline-block divButAction"><a className="butAction" onClick={this.newPO.bind(this)}>New</a></div>
			</div>);

		return footerButtons;
	}

	/** Create element for displaying a progress bar */
	createProgressBar()
	{
		var progressInfo =
			{ 
				progressText: " (" + store.progressTracker.progress + "/" + store.progressTracker.max + ")",
				progressStyle: { width: Math.ceil(100.0 / store.progressTracker.max * store.progressTracker.progress) + "%" }
			};

		if (!this.isBusy)
			setTimeout(() => store.clearProgress(), 500);
		
		return progressInfo;
	}

	onSave() 
	{
		this.setReadOnly(true);
		var po = store.CurrentProductionOrder;
		history.replaceState(po.POID, po.PONUMBER, "card.php?poid="+po.POID);
	}

	onCancel() 
	{
		this.setReadOnly(true);
		var po = store.CurrentProductionOrder;
		history.replaceState(po.POID, po.PONUMBER, "card.php?poid="+po.POID);
	}

	printEstimate() 
	{
		var po = this.isReady ? store.CurrentProductionOrder : null;
		window.open("print/estimate.php?id="+po.POID+"&tn=po_standard", "_blank");
	}

	printInvoice() 
	{
		var po = this.isReady ? store.CurrentProductionOrder : null;
		window.open("print/estimate.php?id="+po.POID+"&tn=po_invoice", "_blank");
	}

	createInvoice() 
	{
		var po = this.isReady ? store.CurrentProductionOrder : null;
		var msg = po.invoiceId > 0 ? "replace the existing" : "create a new";
		if (window.confirm("Are you sure you want to "+msg+" invoice?"))
		{
			po.setUnknownInvoice(); // We created an invoice but we do not know the ID yet.
			window.open("db/createInvoiceFromPO.php?id="+po.POID);
		}
	}

	createPermit() 
	{
		var po = this.isReady ? store.CurrentProductionOrder : null;
		window.open("../amhppermits/buildingpermit_card.php?poid="+po.POID+"&action=createFromPOID&mainmenu=amhppermits", "_self");
	}

	gotoPermit() 
	{
		var po = this.isReady ? store.CurrentProductionOrder : null;
		window.open("../amhppermits/buildingpermit_card.php?id="+po.permitId+"&mainmenu=amhppermits", "_self");
	}

	showInvoice() 
	{
		var po = this.isReady ? store.CurrentProductionOrder : null;
		window.open("../../compta/facture/card.php?facid="+po.invoiceId, "_blank");
	}

	printContract() 
	{
		var po = this.isReady ? store.CurrentProductionOrder : null;
		window.open("print/estimate.php?id="+po.POID+"&tn=po_contract", "_blank");
	}

	printInstOrder() 
	{
		var po = this.isReady ? store.CurrentProductionOrder : null;
		window.open("print/estimate.php?id="+po.POID+"&tn=po_instorder", "_blank");
	}

	printProdOrder() 
	{
		var po = this.isReady ? store.CurrentProductionOrder : null;
		window.open("print/posingle.php?id="+po.POID+"&tn=posingle_standard", "_blank");
	}

	componentDidMount()
	{
		window.onpopstate = (event) => {
			if (Number.parseInt(event.state)>0)
			{
				this.props.store.fetchProductionOrder(event.state).then(
					action("fetchHistory", () => {
						var po = this.props.store.CurrentProductionOrder;
						var allowEdit = po != null && po.Modified || this.props.action == 'edit';
						this.setReadOnly(!allowEdit);
					}));
			}
		}
	}

	copyPO() {
		if (window.confirm("Are you sure you want to copy this estimate?"))
		{
			var po = this.isReady ? store.CurrentProductionOrder : null;
			this.props.store.copyProductionOrder(po).then(
				action("copyProductionOrder", () => {
					var po = this.props.store.CurrentProductionOrder;
					history.pushState(po.POID, po.PONUMBER, "card.php?poid="+po.POID);
				}));
		}
	}

}
