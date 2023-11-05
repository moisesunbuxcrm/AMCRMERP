import { useEffect } from "react"
import { Estimate, isDirty } from "../store/Estimate"
import { EstimateItem } from "../store/EstimateItem"
import { isModified } from "../types/Status"

function useModifiedAlert(estimate:Estimate|undefined, item?:EstimateItem) {
    useEffect(() => {
        const alertUser = (e:BeforeUnloadEvent) => {
            if ((estimate && isDirty(estimate)) || (item && isModified(item._modified))) {
                e.preventDefault()
                e.returnValue = "Please save your changes first."
            }
        }

        window.addEventListener('beforeunload', alertUser)
        return () => {
            window.removeEventListener('beforeunload', alertUser)
        }
    }, [estimate, item])
}

export default useModifiedAlert