export interface TablePaginationProps {
    count: number;
    page: number;
    rowsPerPage: number;
    onPageChange: (
      event: React.MouseEvent<HTMLButtonElement>,
      newPage: number,
    ) => void;
}

export type BlacklistTableProps = {
    // data: {
    //     id: number,
    //     content: string,
    //     observation: string|null,
    // }[],
    // name: string
    blacklist_type: number;
}

export interface DataItem {
    id: number;
    content: string;
    observation: string | null;
}

export interface ResponseData {
    data: DataItem[];
    totalElements: number;
    remainingElementsRight: number;
    remainingElementsLeft: number;
}

export const list_type = {
    1: "ISBN",
    2: "Fornecedor",
    3: "Editora"
}