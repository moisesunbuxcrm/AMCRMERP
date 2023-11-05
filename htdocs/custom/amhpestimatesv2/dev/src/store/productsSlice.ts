import { createSlice, createAsyncThunk } from '@reduxjs/toolkit'
import { LoadingState } from '../types/Status'
import * as mock from './mock/products-mock'
import { RootState } from './store'
import Fraction from 'fraction.js'
import ModuleType from '../types/ModuleType'
import WinType from '../types/WinType'

export type ProductSummary = {
  color: string
  glass_color:string
  heighttxt: string
  id: number
  interlayer:string
  itemtype: number
  lengthtxt: string
  modtype: ModuleType
  name: string
  provider: string
  ref: string
  widthtxt: string
  wintype: WinType
}

export type ProductDetails = {
  coating:string
  color: string
  configuration: string
  cost_price: number
  frame_color: string
  glass_color:string
  glass_type:string
  hardwaretype: string
  height: number
  heighttxt: string
  id: number
  image: string
  inst_price: number
  interlayer:string
  is_screen: boolean
  itemtype: number
  length: number
  lengthtxt: string
  modtype: ModuleType
  name: string
  provider: string
  ref: string
  sales_price: number
  width: number
  widthtxt: string
  wintype: WinType
}

export type ProductDetailsMap = { 
  [key: string]: ProductDetails 
}

export type ProductDetailsRequests = { 
  [id: string]: boolean 
}

export type ProductsState = {
    productSummaries: ProductSummary[]
    productSummariesById: { [key: number]: ProductSummary }
    productSummariesByRef: { [key: string]: ProductSummary }
    productSummariesStatus: LoadingState,

    productDetails: ProductDetailsMap
    productDetailsStatus: LoadingState,

    error: string | null | undefined
}

const initialState : ProductsState = {
  productSummaries: [],
  productSummariesById: {},
  productSummariesByRef: {},
  productSummariesStatus: LoadingState.unavailable,
  productDetails: {},
  productDetailsStatus: LoadingState.unavailable,
  error: null,
}

export const fetchProductSummaries = createAsyncThunk<ProductSummary[]>('fetchProductSummaries', async (arg:void, thunkAPI:any) => {
  console.log("Fetching product summaries")
  if (process.env.NODE_ENV === "production") {
    const response = await fetch(`http://${window.location.hostname}/AMCRMERP/htdocs/custom/amhpestimatesv2/db/getproductsummaries.php`)
    const json:ProductSummary[] = await response.json();
    return json;
  }
  
  // For testing in dev
  return mock.ProductSummaries 
})

export const fetchProductDetailsById = createAsyncThunk<ProductDetails, number>('fetchProductDetailsById', async (id:number, thunkAPI:any) => {
  let productsState:ProductsState = thunkAPI.getState().products
  if (productsState.productDetails[id])
    return productsState.productDetails[id]

  if (process.env.NODE_ENV === "production") {
    console.log("Fetching product ", id)
    const response = await fetch(`http://${window.location.hostname}/AMCRMERP/htdocs/custom/amhpestimatesv2/db/getproductdetails.php?id=${id}`)
    const json:any = await response.json();
    let results = null;
    if (Array.isArray(json) && json.length>0)
      results = json[0];
    else
      results = json;

    const wf = new Fraction(results.widthtxt ?? 0)
    const hf = new Fraction(results.heighttxt ?? 0)
    const lf = new Fraction(results.lengthtxt ?? 0)
    const w = results.width?Number(results.width):(results.widthtxt?wf.valueOf():0)
    const h = results.height?Number(results.height):(results.heighttxt?hf.valueOf():0)
    const l = results.length?Number(results.length):(results.lengthtxt?lf.valueOf():0)

    results = {
      ...results,
      id: Number(results.id),
      sales_price: Number(results.sales_price),
      inst_price: Number(results.inst_price),
      cost_price: Number(results.cost_price),
      width: w,
      height: h,
      length: l,
      is_screen: Boolean(results.is_screen)
    }
    return results as ProductDetails;
  }
  
  // For testing in dev
  return mock.GetProductDetails(id)
})

export const productsSlice = createSlice({
  name: 'products',
  initialState,
  reducers: {
  },
  extraReducers: (builder) => {

    builder.addCase(fetchProductSummaries.pending, (state, action) => {
      state.productSummariesStatus = LoadingState.loading
    })
    builder.addCase(fetchProductSummaries.fulfilled, (state, action) => {
      state.productSummariesStatus = LoadingState.succeeded
      state.productSummaries = action.payload
      state.productSummaries.forEach(ps => state.productSummariesById[ps.id] = ps)
      state.productSummaries.forEach(ps => state.productSummariesByRef[ps.ref] = ps)
    })
    builder.addCase(fetchProductSummaries.rejected, (state, action) => {
      state.productSummariesStatus = LoadingState.failed
      state.error = action.error.message
    })

    builder.addCase(fetchProductDetailsById.pending, (state, action) => {
      state.productDetailsStatus = LoadingState.loading
    })
    builder.addCase(fetchProductDetailsById.fulfilled, (state, action) => {
      state.productDetailsStatus = LoadingState.succeeded
      state.productDetails[action.payload.id] = action.payload 
    })
    builder.addCase(fetchProductDetailsById.rejected, (state, action) => {
      state.productDetailsStatus = LoadingState.failed
      state.error = action.error.message
    })

  }
})

export const selectProductSummaries = (state:RootState) : ProductSummary[]|undefined => state.products.productSummaries
export const selectProductSummariesById = (state:RootState) : { [key: number]: ProductSummary }|undefined => state.products.productSummariesById
export const selectProductSummariesByRef = (state:RootState) : { [key: string]: ProductSummary }|undefined => state.products.productSummariesByRef
export const selectProductSummariesStatus = (state:RootState) : LoadingState => state.products.productSummariesStatus

export const selectProductDetails = (state:RootState) : ProductDetailsMap|undefined => state.products.productDetails
export const selectProductDetailsStatus = (state:RootState) : LoadingState => state.products.productDetailsStatus

export default productsSlice.reducer