import { createSlice, createAsyncThunk, PayloadAction } from '@reduxjs/toolkit'
import { EstimateStatus } from '../types/EstimateStatus'
import ModuleType from '../types/ModuleType'
import { isModified, LoadingState, ModifiedState } from '../types/Status'
import { Estimate, formatDate } from './Estimate'
import { EstimateItem } from './EstimateItem'
import * as mock from './mock/estimates-mock'
import { RootState } from './store'

export type EstimatesState = {
    estimate?: Estimate
    estimateStatus: LoadingState
    error?: string
    nextItemId: number
}

const initialState : EstimatesState = {
  estimate: undefined,
  estimateStatus: LoadingState.unavailable,
  nextItemId: -1
}

const cleanNotes = (s:string) => {
  s = s.replace("\\n", "\n")
  s = s.replace("&qt;", "\"")
  return s;
}

export const fetchEstimate = createAsyncThunk<Estimate, number>('fetchEstimate', async (id:number) => {
  return _fetchEstimate(id)
})

export const _fetchEstimate = async (id:number) => {
  console.log("Fetching estimate ", id)
  if (process.env.NODE_ENV === "production") {
    const response = await fetch(`http://${window.location.hostname}/AMCRMERP/htdocs/custom/amhpestimatesv2/db/getEstimate.php?id=${id}`)
    const json:any = await response.json();
    if (json.msg) 
      throw json.msg
    json._modified = ModifiedState.none;
    if (json.notes)
      json.notes = cleanNotes(json.notes)
    if (json.public_notes)
      json.public_notes = cleanNotes(json.public_notes)
    if (json.items)
      for (const i in json.items)
        json.items[i]._modified = ModifiedState.none;
    return {
      ...json,
        new_construction_owner_responsability: !!json.new_construction_owner_responsability,
        pay_upon_completion: !!json.pay_upon_completion,
    }
  }
  
  // For testing in dev
  let e = mock.GetEstimate(id)
  let items = []
  for (const i in e.items)
    items.push({
      ...e.items[i],
      _modified: ModifiedState.none
    })
  return {
    ...e,
    items,
    _modified: ModifiedState.none
  }
}

/** Creates a query string for a URL using the properties of the object */
const obj2URL = (o:any) => {
  let url:string = Object.keys(o).reduce((s:string, k:string) =>
    s + ((o[k] !== undefined && typeof(o[k]) !== "object") ? `&${k}=${encodeURIComponent(o[k])}` : ""), "")
  return url.startsWith("&")?url.substr(1,url.length-1):""
}

const saveItem = async (item:EstimateItem):Promise<EstimateItem> => {
  let newItem:EstimateItem = {...item}
  let updateFileName = ""
  switch(item.modtype) {
    case ModuleType.ImpactProduct:
      updateFileName = "EstimateImpactProduct"
      break;
    case ModuleType.Hardware:
      updateFileName = "EstimateHardware"
      break;
    case ModuleType.Material:
      updateFileName = "EstimateMaterial"
      break;
    case ModuleType.Design:
      updateFileName = "EstimateDesign"
      break;
  }
  
  if (process.env.NODE_ENV === "production" && updateFileName !== "") {

    // UPDATE ITEM
    if (item._modified === ModifiedState.modified) {
      const response = await fetch(
        `http://${window.location.hostname}/AMCRMERP/htdocs/custom/amhpestimatesv2/db/update${updateFileName}.php`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: obj2URL(item)
        })
      const json:any = await response.json();
      if (json.error) 
        throw json.error
      newItem = {
        ...item
      }
    }

    // CREATE ITEM
    if (item._modified === ModifiedState.new) {
      const response = await fetch(
        `http://${window.location.hostname}/AMCRMERP/htdocs/custom/amhpestimatesv2/db/create${updateFileName}.php`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: obj2URL(item)
        })
      const json:any = await response.json();
      if (json.error) 
        throw json.error
      newItem = {
        ...item,
        id:json.id,
        estimateitemid:json.estimateitemid
      } as any
    }

    // DELETE ITEM
    if (item._modified === ModifiedState.deleted) {
      const response = await fetch(
        `http://${window.location.hostname}/AMCRMERP/htdocs/custom/amhpestimatesv2/db/delete${updateFileName}.php`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: "id="+item.id
        })
      const json:any = await response.json();
      if (json.error) 
        throw json.error
    }
  }
    
  return {
    ...newItem,
    _modified: ModifiedState.none
  }
}

export const saveEstimate = createAsyncThunk<Estimate, Estimate>('saveEstimate', async (estimate:Estimate) => {
  let newEstimate:Estimate = {...estimate}
  let newItems:EstimateItem[] = []
  
  console.log("Saving estimate ", estimate)
  if (process.env.NODE_ENV === "production") {

    // UPDATE ESTIMATE
    if (estimate._modified === ModifiedState.modified) {
      const response = await fetch(
        `http://${window.location.hostname}/AMCRMERP/htdocs/custom/amhpestimatesv2/db/updateEstimate.php`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: obj2URL(estimate)
        })
      const json:any = await response.json();
      if (json.msg) 
        throw json.msg
      if (json.notes)
        json.notes = cleanNotes(json.notes)
      if (json.public_notes)
        json.public_notes = cleanNotes(json.public_notes)
      newEstimate = {
        ...json,
        new_construction_owner_responsability: !!json.new_construction_owner_responsability,
        pay_upon_completion: !!json.pay_upon_completion,
      }
    }

    // CREATE ESTIMATE
    if (estimate._modified === ModifiedState.new) {
      const response = await fetch(
        `http://${window.location.hostname}/AMCRMERP/htdocs/custom/amhpestimatesv2/db/createEstimate.php`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: obj2URL(estimate)
        })
      const json:any = await response.json();
      if (json.msg) 
        throw json.msg
      if (json.notes)
        json.notes = cleanNotes(json.notes)
      if (json.public_notes)
        json.public_notes = cleanNotes(json.public_notes)
      newEstimate = {
        ...json,
        new_construction_owner_responsability: !!json.new_construction_owner_responsability,
        pay_upon_completion: !!json.pay_upon_completion,
      }
    }

    // Update items
    let promises:Promise<void>[] = []
    estimate.items?.forEach(i => {
      promises.push((async () => {
        if (isModified(i._modified)) {
          let newItem = await saveItem({
            ...i,
            estimateid: newEstimate.id || 0,
          })
          if (i._modified !== ModifiedState.deleted)
            newItems.push(newItem)
        }
        else
          newItems.push({...i})
      })())
    })
    await Promise.all(promises)
  }
  else {
    if (!newEstimate.id)
      newEstimate.id = 999 // simulate a new estimate being created
    newItems = newEstimate.items?.filter(i => i._modified !== ModifiedState.deleted).map(i => ({
      ...i,
      _modified: ModifiedState.none
    }))
  }

  return {
    ...newEstimate,
    items: newItems?.map(i => ({
      ...i,
      _modified: ModifiedState.none
    })),
    _modified: ModifiedState.none
  }
})

export const duplicateEstimate2 = createAsyncThunk<Estimate, Estimate>('duplicateEstimate2', async (estimate:Estimate) => {  
  let newEstimate:Estimate = {...estimate}

  if (process.env.NODE_ENV === "production") {
    const response = await fetch(
      `http://${window.location.hostname}/AMCRMERP/htdocs/custom/amhpestimatesv2/db/copyEstimate.php`,
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: obj2URL({id: estimate.id})
      })
    const json:any = await response.json();
    if (json.msg && json.msg !== "OK") 
      throw json.msg
    newEstimate = await _fetchEstimate(json.newID)
  }
  else {
    newEstimate = await _fetchEstimate((newEstimate.id||0) + 1000)
  }
  return {
    ...newEstimate,
  }
})

// const _updateItemNumbers = (items:EstimateItem[]) => {
//   let index = 0;
//   return items.map((item:EstimateItem) => {
//     index++;
//     if (item.itemno !== index)
//       return {...item, itemno: index}
//     return item
//   })
// }

export const estimatesSlice = createSlice({
  name: 'estimate',
  initialState,
  reducers: {
    createEstimate: (state, action:PayloadAction<Estimate>) => {
      state.estimate = {
        ...action.payload,
        customer: action.payload.customer ? {...action.payload.customer} : undefined,
        items: action.payload.items?.map(i => ({...i})),
        _modified: isModified(action.payload._modified) ? action.payload._modified : ModifiedState.new
      }
    },
    duplicateEstimate: (state, action:PayloadAction<Estimate>) => {
      state.estimate = {
        ...action.payload,
        id: undefined,
        estimatenum:undefined,
        quotedate: formatDate(new Date()),
        status: EstimateStatus.InProgress,
        status_reason: undefined,
        approved_date: undefined,
        rejected_date: undefined,
        delivered_date: undefined,
        customer: action.payload.customer ? {
          ...action.payload.customer
        } : undefined,
        permitId: undefined,
        items: action.payload.items?.map(i => ({
          ...i,
          id: undefined,
          estimateid: 0,
          _modified: ModifiedState.new
        })),
        _modified: ModifiedState.new
      }
    },
    updateEstimate: (state, action:PayloadAction<Estimate>) => {
      state.estimate = {
        ...action.payload,
        customer: action.payload.customer ? {...action.payload.customer} : undefined,
        items: action.payload.items?.map(i => ({...i})),
        _modified: isModified(action.payload._modified) ? action.payload._modified : ModifiedState.modified
      }
    },
    updateItem: (state, action:PayloadAction<EstimateItem>) => {
      let updatedItem = {
        ...action.payload,
        _modified: isModified(action.payload._modified) ? action.payload._modified : ModifiedState.modified
      }
      let items:EstimateItem[] = [];
      if (state.estimate && state.estimate.items) {
        items = state.estimate.items.map(i => i.id === action.payload.id ? updatedItem : i)          
        state.estimate = {
          ...state.estimate,
          items: items
        }
      }
    },
    deleteItem: (state, action:PayloadAction<EstimateItem>) => {
      if (state.estimate) {
        let items:EstimateItem[] = [];
        if (state.estimate.items)
          items = state.estimate.items.filter(i => i.itemno !== action.payload.itemno)
        if (action.payload._modified !== ModifiedState.new)
          items.push({
            ...action.payload,
            _modified: ModifiedState.deleted
          })
        state.estimate = {
          ...state.estimate,
          items: items,
          _modified: state.estimate._modified
        }
      }
    },
    copyItem: (state, action:PayloadAction<EstimateItem>) => {
      if (state.estimate) {
        let items:EstimateItem[] = [];
        if (state.estimate.items) {
          items = [
            ...state.estimate.items, 
            {
              ...action.payload, 
              id: state.nextItemId--, 
              itemno: state.estimate.items.map(i => i.itemno||0).reduce((m,v) => m<v?v:m, 0)+1,
              _modified: ModifiedState.new
            }
          ]
          state.estimate = {
            ...state.estimate,
            items: items,
            _modified: state.estimate._modified
          }
        }
      }
    },
    addItem: (state, action:PayloadAction<EstimateItem>) => {
      if (state.estimate) {
        let items:EstimateItem[] = state.estimate.items;
        items = [
          ...state.estimate.items, 
          {
            ...action.payload, 
            id: state.nextItemId--, 
            itemno: state.estimate.items ? state.estimate.items.map(i => i.itemno||0).reduce((m,v) => m<v?v:m, 0)+1 : 1,
            _modified: ModifiedState.new
          }
        ]
        state.estimate = {
          ...state.estimate,
          items: items,
          _modified: state.estimate._modified
        }
      }
    }
  },
  extraReducers: (builder) => {
    builder.addCase(fetchEstimate.pending, (state) => {
      state.estimateStatus = LoadingState.loading
    })
    builder.addCase(fetchEstimate.fulfilled, (state, action) => {
      if ((action.payload as any).err) {
          state.estimateStatus = LoadingState.failed
          state.estimate = undefined
          state.error = (action.payload as any).err
      }
      else {
          state.estimateStatus = LoadingState.succeeded
          state.estimate = action.payload
          state.error = undefined
      }
    })
    builder.addCase(fetchEstimate.rejected, (state, action) => {
      state.estimateStatus = LoadingState.failed
      state.error = action.error.message
    })

    builder.addCase(saveEstimate.pending, (state) => {
      state.estimateStatus = LoadingState.saving
    })
    builder.addCase(saveEstimate.fulfilled, (state, action) => {
      state.estimateStatus = LoadingState.succeeded
      state.estimate = action.payload
      state.error = undefined
    })
    builder.addCase(saveEstimate.rejected, (state, action) => {
      state.estimateStatus = LoadingState.failed
      state.error = action.error.message
    })
  }
})

export const { createEstimate, updateEstimate, duplicateEstimate, updateItem, deleteItem, copyItem, addItem } = estimatesSlice.actions

export const selectEstimate = (state:RootState) : Estimate|undefined => state.estimates.estimate
export const selectEstimateStatus = (state:RootState) : LoadingState => state.estimates.estimateStatus
export const selectEstimateError = (state:RootState) : string => state.estimates.error??""

export default estimatesSlice.reducer