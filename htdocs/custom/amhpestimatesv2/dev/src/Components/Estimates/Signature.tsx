import { Col } from "antd";

export type SignatureProps = {
    title:string
    name:string
    className?:string
    width:number
}

const TitleCol = (props:any) => <Col xs={props.width} md={props.width/4} className={"signature-title-cell"+(props.className?" "+props.className+"-cell":"")}><div className={"signature-title-text"+(props.className?" "+props.className+"-text":"")}>{props.children}</div></Col>
const NameCol = (props:any) => <Col xs={props.width} md={props.width*3/4} className={"signature-name-cell"+(props.className?" "+props.className+"-cell":"")}><div className={"signature-name-text"+(props.className?" "+props.className+"-text":"")}>{props.children}</div></Col>

const Signature = ({title, name, className, width}: SignatureProps) => {
    return (
        <>
            <TitleCol width={width} className={className}>{title}:</TitleCol>
            <NameCol width={width} className={className}>{name}</NameCol>
        </>)
}

export default Signature;