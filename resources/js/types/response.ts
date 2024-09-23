interface IBaseResponse {
    id: string;
    name: string;
}

export interface IPaginationMeta {
    current_page: number;
    per_page: number;
    last_page: number;
    total: number;
}

export interface ICategory extends IBaseResponse { };
export interface IRack extends IBaseResponse { }
export interface IBrand extends IBaseResponse { }
export interface IProduct extends IBaseResponse {
    stock: number;
    buy_price: number;
    sale_price: number;
    category: string;
    rack: string;
}
export interface IProductForm extends IBaseResponse {
    stock: number;
    buy_price: number;
    sale_price: number;
    category_id: string;
    rack_id: string;
}
export interface IEmployee extends IBaseResponse {
    gender: string;
    address: string;
    phone: string;
    whatsapp: string;
}
export interface ISupplier extends IBaseResponse {
    contact_name: string;
    address: string;
    phone: string;
    whatsapp: string;
}
