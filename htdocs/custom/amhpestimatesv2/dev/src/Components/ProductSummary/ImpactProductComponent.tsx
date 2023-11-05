import { useAppSelector } from '../../store/hooks';
import { ImpactProduct } from '../../store/ImpactProduct';
import ProductComponent, { ProductComponentProps } from './ProductComponent';
import * as ds from '../../store/defaultsSlice';
import { LoadingState } from '../../types/Status';

export interface ImpactProductProps extends ProductComponentProps {
    item: ImpactProduct
    readOnly: boolean
}

const ImpactProductComponent = ({ item, ...rest }: ImpactProductProps) => {
    const defaults: ds.DefaultData | undefined = useAppSelector(ds.selectDefaults)
    const defaultStatus = useAppSelector(ds.selectDefaultsStatus)
    const id2RoomType = (id?:string):string|undefined => defaults?.roomtypes ? defaults.roomtypes.find(rt => rt.value === id)?.label : undefined
    const roomtype = (defaultStatus === LoadingState.loading || item.roomtype === undefined) 
        ? "Unknown"
        : id2RoomType(item.roomtype.toString())

    return (
        <ProductComponent
            item={item} 
            description={
                <>
                    <p className='table_text'>Screen: {item.is_screen?"Yes":"No"}</p>
                    <p className='table_text'>Size: {item.widthtxt||"?"} x {item.heighttxt||"?"} x {item.lengthtxt||"?"}</p>
                    <p className='table_text'>Glass Type: {item.glass_type}</p>
                    <p className='table_text'>Glass Color: {item.glass_color}</p>
                    <p className='table_text'>Interlayer: {item.interlayer}</p>
                    <p className='table_text'>Coating: {item.coating}</p>
                    <p className='table_text'>Type of Room: {roomtype}</p>
                    <p className='table_text'>Room {item.roomnum}</p>
                </>
            }

            {...rest}
        />)
};

export default ImpactProductComponent;

