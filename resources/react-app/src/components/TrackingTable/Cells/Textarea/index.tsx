import { useEffect, useState } from "react"
import updateField from "../../functions/updateField"
import { TextareaProp } from "./types"

export const Textarea = (props: TextareaProp) => {
  const { defaultValue, tracking_code, field_name } = props

  return (
    <textarea 
      onKeyDown={(event) => {
        if(event.key !== "Enter") return
        const input = event.target as HTMLInputElement
        updateField(tracking_code, input, field_name)
        input.blur()
      }} 
      defaultValue={defaultValue}
    ></textarea>
  )
}