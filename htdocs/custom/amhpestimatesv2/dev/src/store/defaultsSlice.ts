import { createSlice, createAsyncThunk } from '@reduxjs/toolkit'
import { LoadingState } from '../types/Status';
import * as mock from './mock/defaults-mock';
import { RootState } from './store'

export type UserData = {
  name: string
  admin: boolean
  phone: string
}

export type CompanyData = {
  name: string
  address: string
  town: string
  zip: string
  state: string
  phone: string
  fax: string
}

export type LabeledData = {
  value: string
  label: string
}

export type DefaultData = {
  user: UserData
  company: CompanyData
  colors: LabeledData[]
  providers: LabeledData[]
  roomtypes: LabeledData[]
};

const InitialData = {
  user: { name: "", admin: false, phone: ""},
  company: { name: "", address: "", town: "", zip: "", state: "", phone: "", fax: ""},
  colors: [],
  providers: [],
  roomtypes: [],
};

export type DefaultsState = {
    defaults?: DefaultData
    defaultsStatus: LoadingState,

    error?: string
}

const initialState : DefaultsState = {
  defaults: InitialData,
  defaultsStatus: LoadingState.unavailable
}

export const fetchDefaults = createAsyncThunk<DefaultData>('getDefaults', async (arg:void, thunkAPI:any) => {
  if (process.env.NODE_ENV === "production") {
    const response = await fetch(`http://${window.location.hostname}/AMCRMERP/htdocs/custom/amhpestimatesv2/formdata.js.php`)
    const json:DefaultData = await response.json();
    return json;
  }
  
  // For testing in dev
  return mock.Defaults
})

export const defaultsSlice = createSlice({
  name: 'defaults',
  initialState,
  reducers: {
  },
  extraReducers: (builder) => {

    builder.addCase(fetchDefaults.pending, (state, action) => {
      state.defaultsStatus = LoadingState.loading
    })
    builder.addCase(fetchDefaults.fulfilled, (state, action) => {
      state.defaultsStatus = LoadingState.succeeded
      state.defaults = action.payload
    })
    builder.addCase(fetchDefaults.rejected, (state, action) => {
      state.defaultsStatus = LoadingState.failed
      state.error = action.error.message
    })
  }
})

export const selectDefaults = (state:RootState) : DefaultData|undefined => state.defaults.defaults
export const selectDefaultsStatus = (state:RootState) : LoadingState => state.defaults.defaultsStatus
export const selectDefaultsError = (state:RootState) : string => state.defaults.error ? state.defaults.error : ""

export type RoomTypeConverterFunction = (id?:string|undefined) => string|undefined
/** This selector returns a function that knows how to convert a room type id into a string. The string is the roomtype name if the room types have been loaded from the database, otherwise it is just the id */
export const selectId2RoomTypeConvertor = (state:RootState):RoomTypeConverterFunction => 
  (id?:string|undefined) => {
    let label
    if (state.defaults.defaults?.roomtypes)
      label = state.defaults.defaults.roomtypes.find(rt => rt.value === id)?.label

    if (!label)
      label = id

    return label
  }

export const selectRoomType2IdConvertor = (state:RootState):RoomTypeConverterFunction => 
  (type?:string|undefined) => {
    let value
    if (state.defaults.defaults?.roomtypes)
      value = state.defaults.defaults.roomtypes.find(rt => rt.label === type)?.value

    if (!value)
      value = type

    return value
  }

export default defaultsSlice.reducer