import getXLSXBlingFile from "./getXLSXBlingFile"
import XLSXFileData from "./types"

const XLSX_FILE_FUNCTIONS = {
  'Bling': getXLSXBlingFile
} as {
  [key: string]: (xlsxData: XLSXFileData) => Promise<{file: File, name: string}>
}

const getXLSXFileFor = (fileFor: string, xlsxData: XLSXFileData) => XLSX_FILE_FUNCTIONS[fileFor](xlsxData)

export default getXLSXFileFor
