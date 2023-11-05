export enum LoadingState {
    unavailable = "unavailable",
    loading = "loading",
    saving = "saving",
    succeeded = "succeeded",
    failed = "failed"
}

export enum ModifiedState {
    none = "none",
    new = "new",
    modified = "modified",
    deleted = "deleted"
}

export const isNew = (s:ModifiedState) => s === ModifiedState.new
export const isModified = (s:ModifiedState) => s !== ModifiedState.none
export const isDeleted = (s:ModifiedState) => s === ModifiedState.deleted

