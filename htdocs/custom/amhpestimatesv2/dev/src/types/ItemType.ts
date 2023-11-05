enum ItemType {
    None = "",
    Window = "Window",
    Door = "Door",
    Hardware = "Hardware",
}

export const ItemTypes:string[] = Object.keys(ItemType).filter(key => isNaN(Number(key)))

export const id2ItemType = (n:number):ItemType =>
{
    return ItemTypes[n] as ItemType
}

export const itemType2Id = (i:string):number =>
{
    return ItemTypes.indexOf(i)
}

export default ItemType 