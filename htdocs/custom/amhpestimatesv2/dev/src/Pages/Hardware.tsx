import React, { useEffect, useState } from 'react';
import '../Styles/main.css';
import { Layout, Spin } from 'antd';
import * as es from '../store/estimatesSlice';
import { useAppDispatch, useAppSelector } from '../store/hooks';
import HardwareDetail from '../Components/ProductDetails/Hardware';
import { EstimateItem } from '../store/EstimateItem';
import { useHistory, useParams } from 'react-router';
import { Hardware, DefaultHardware } from '../store/Hardware';
import { Estimate } from '../store/Estimate';
import { ModifiedState } from '../types/Status';
import { isReadOnly } from '../types/EstimateStatus';

const { Content } = Layout;

const HardwarePage = () => {
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
                ...DefaultHardware,
                estimateid: estimate && estimate.id ? estimate.id : 0,
                _modified: ModifiedState.new
            })
    }, [dispatch, params.eid, estimate])

    useEffect(() => {
        if (params.iid && estimate && estimate.items)
            setItem(estimate.items.find(i => i.id === Number(params.iid)))
    }, [estimate, params.iid])

    const readOnly = isReadOnly(estimate)

    return (
        <Layout className='hardware'>
            <Content className='hardwareContainer'>
                {item 
                    ?   <HardwareDetail
                            estimate={estimate}
                            item={item as Hardware}
                            onClose={history.length>0?()=>history.goBack():undefined}
                            readOnly={readOnly}
                        /> 
                    : <Spin size="large" style={{ display: "flex", alignSelf: "center" }} />}
            </Content>
        </Layout>
    );
};

export default HardwarePage;