import { configureStore } from '@reduxjs/toolkit'
import customersReducer from './customersSlice'
import estimatesReducer from './estimatesSlice'
import defaultsReducer from './defaultsSlice'
import productsReducer from './productsSlice'

const store = configureStore({
  reducer: {
    customers: customersReducer,
    estimates: estimatesReducer,
    defaults: defaultsReducer,
    products: productsReducer
  }
})

// Infer the `RootState` and `AppDispatch` types from the store itself
export type RootState = ReturnType<typeof store.getState>
export type AppDispatch = typeof store.dispatch

export default store;
