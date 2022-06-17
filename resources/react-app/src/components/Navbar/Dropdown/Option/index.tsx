import { OptionProp } from "./types"

export const Option = (props: OptionProp) => {
    const { label, pathname } = props

    return (
        <a href={pathname}>{label}</a>
    )
}