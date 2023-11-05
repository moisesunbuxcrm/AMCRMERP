import { Layout } from "antd";
import { Content } from "antd/lib/layout/layout";
import Invoice from "../Components/Estimates/Invoice";
import PrintHeader from "../Components/Estimates/PrintHeader";

interface EstimateProps {
    viewMenu: (eid:number) => JSX.Element
} 

const InvoiceView = ({viewMenu}:EstimateProps) => {
    return (
        <Layout className={"print-estimate"}>
            <Content className='estimateContainer'>
                <PrintHeader />
                <Invoice viewMenu={viewMenu}/>
            </Content>
        </Layout>
    );
};

export default InvoiceView