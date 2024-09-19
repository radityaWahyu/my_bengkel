export interface IPaginationMeta {
    current_page: number;
    per_page: number;
    last_page: number;
    total: number;
}


export interface ICategory {
    id: string;
    name: string;
};

export interface IRack extends ICategory { }
export interface IBrand extends ICategory { }