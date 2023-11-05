import { Estimate } from "../Estimate"
import { ValidationHelpers, EstimateProblem, PendingProblems } from "../validations"

/**
 * Check all french door or fiberglass have hardware
 */
export const checkRequiredHardware = (e:Estimate|undefined, h:ValidationHelpers):EstimateProblem[] => {
    const problems:EstimateProblem[] = []

    // Check if room type data is loaded
    const bedroomTypeId = h.roomType2Id("Bedroom")
    if (!bedroomTypeId)
        return [PendingProblems]

    return problems
}

