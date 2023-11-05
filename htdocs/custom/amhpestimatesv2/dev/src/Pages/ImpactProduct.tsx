import React, { useEffect, useState } from 'react';
import '../Styles/main.css';
import { Layout, Spin } from 'antd';
import * as es from '../store/estimatesSlice';
import { useAppDispatch, useAppSelector } from '../store/hooks';
import ImpactProductDetail from '../Components/ProductDetails/ImpactProduct';
import { EstimateItem } from '../store/EstimateItem';
import { useHistory, useParams } from 'react-router';
import { DefaultImpactProduct, ImpactProduct } from '../store/ImpactProduct';
import { Estimate } from '../store/Estimate';
import { ModifiedState } from '../types/Status';
import { isReadOnly } from '../types/EstimateStatus';

const { Content } = Layout;

const ImpactProductPage = () => {
    const dispatch = useAppDispatch();
    const [item, setItem] = useState<EstimateItem | undefined>(undefined)
    const estimate: Estimate | undefined = useAppSelector(es.selectEstimate)
    var params = useParams<{eid: string, iid: string}>();
    const history = useHistory();

    useEffect(() => {
        // If we know the eid then load the estimate
        if (params.eid && estimate === undefined)
            dispatch(es.fetchEstimate(Number(params.eid)))
        else
            setItem({
                ...DefaultImpactProduct,
                estimateid: estimate && estimate.id ? estimate.id : 0,
                _modified: ModifiedState.new
            })
    }, [dispatch, params.eid, estimate])

    useEffect(() => {
        if (params.iid && estimate && estimate.items)
            setItem(estimate.items.find(i => i.id === Number(params.iid)))
    }, [estimate, params.iid])

    const onClose = (addHardware:boolean) => {
        if (addHardware && estimate && estimate.id)
            history.replace(`/hardware/${estimate.id}`);
        else if (history.length>0)
            history.goBack()
    }

    const readOnly = isReadOnly(estimate)
    
    return (
        <Layout className='impact'>
            <Content className='impactContainer'>
                {item 
                    ?   <ImpactProductDetail
                            estimate={estimate}
                            item={item as ImpactProduct}
                            onClose={onClose}
                            readOnly={readOnly}
                        /> 
                    : <Spin size="large" style={{ display: "flex", alignSelf: "center" }} />}
            </Content>
        </Layout>
    );
};

export default ImpactProductPage;