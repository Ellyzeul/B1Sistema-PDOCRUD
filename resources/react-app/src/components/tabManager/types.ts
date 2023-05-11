export type TabManagerProp = {
    props: { 
        contents: {[key: string]: JSX.Element},
        options: {label: string, key: string}[]
     }
}
  