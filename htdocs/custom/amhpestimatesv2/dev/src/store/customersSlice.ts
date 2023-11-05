import { createSlice, createAsyncThunk } from '@reduxjs/toolkit'
import { LoadingState } from '../types/Status'
import * as mock from './mock/customers-mock'
import { RootState } from './store'

export const RelationshipType = ["None", "Home Owner", "Dealer", "Contractor", "Walk in"]
export const MarkupByRelationshipType = [0, .45, .17, .35, .45]
export const reltype2Markup = (t:number|undefined) => t ? MarkupByRelationshipType[t] : 0 

export type CustomerName = {
  value: string
  label: string
}

export type CustomerDetails = {
  contactaddress: string
  contactmobile: string
  contactname: string
  contactphone: string
  customeraddress: string
  customercity: string
  customeremail: string
  customermobile: string
  customername: string
  customerphone: string
  customerstate: string
  customerzip: string
  folionumber: string
  id: number
  reltype: number
}

export type CustomerDetailsMap = { 
  [key: string]: CustomerDetails 
}

export type CustomerDetailsRequests = { 
  [id: string]: boolean 
}

export type CustomersState = {
    customerNames: CustomerName[]
    customerNamesStatus: LoadingState,

    customerDetails: CustomerDetailsMap
    customerDetailsStatus: LoadingState,
    customerDetailsRequested: CustomerDetailsRequests

    error: string | null | undefined
}

const initialState : CustomersState = {
  customerNames: [],
  customerNamesStatus: LoadingState.unavailable,
  customerDetails: {},
  customerDetailsStatus: LoadingState.unavailable,
  customerDetailsRequested: {},
  error: null,
}

export const fetchCustomerNames = createAsyncThunk<CustomerName[]>('getCustomerNames', async () => {
  console.log("Fetching customer names ")
  if (process.env.NODE_ENV === "production") {
    const response = await fetch(`http://${window.location.hostname}/AMCRMERP/htdocs/custom/amhpestimatesv2/db/getcustomernames.php`)
    const json:CustomerName[] = await response.json();
    return json;
  }
  
  // For testing in dev
  return mock.CustomerNames 
})

export const fetchCustomerDetailsById = createAsyncThunk<CustomerDetails, number>('getCustomerDetails', async (id:number) => {
  console.log("Fetching customer ", id)
  if (process.env.NODE_ENV === "production") {
    const response = await fetch(`http://${window.location.hostname}/AMCRMERP/htdocs/custom/amhpestimatesv2/db/getcustomerdetails.php?id=${id}`)
    const json:any = await response.json();
    let results = null;
    if (json.length && json.length>0)
      results = json[0];
    else
      results = json;
    return results as CustomerDetails;
  }
  
  // For testing in dev
  let cn = mock.CustomerNames.find(cm => cm.value === id.toString())?.label
  return mock.GetCustomerDetails(id, cn??id.toString())
})

export const customersSlice = createSlice({
  name: 'customers',
  initialState,
  reducers: {
  },
  extraReducers: (builder) => {

    builder.addCase(fetchCustomerNames.pending, (state, action) => {
      state.customerNamesStatus = LoadingState.loading
    })
    builder.addCase(fetchCustomerNames.fulfilled, (state, action) => {
      state.customerNamesStatus = LoadingState.succeeded
      state.customerNames = action.payload
    })
    builder.addCase(fetchCustomerNames.rejected, (state, action) => {
      state.customerNamesStatus = LoadingState.failed
      state.error = action.error.message
    })

    builder.addCase(fetchCustomerDetailsById.pending, (state, action) => {
      state.customerDetailsStatus = LoadingState.loading
    })
    builder.addCase(fetchCustomerDetailsById.fulfilled, (state, action) => {
      state.customerDetailsStatus = LoadingState.succeeded
      if (state.customerDetails && action.payload?.id) {
        state.customerDetails[action.payload.id] = action.payload 
      }
    })
    builder.addCase(fetchCustomerDetailsById.rejected, (state, action) => {
      state.customerDetailsStatus = LoadingState.failed
      state.error = action.error.message
    })

  }
})

export const selectCustomerNames = (state:RootState) : CustomerName[]|undefined => state.customers.customerNames
export const selectCustomerNamesStatus = (state:RootState) : LoadingState => state.customers.customerNamesStatus

export const selectCustomerDetails = (state:RootState) : CustomerDetailsMap|undefined => state.customers.customerDetails
export const selectCustomerDetailsStatus = (state:RootState) : LoadingState => state.customers.customerDetailsStatus

export default customersSlice.reducer