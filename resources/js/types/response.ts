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
export interface IRepair extends IBaseResponse {
    price: number;
}
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

export interface ICustomer extends IEmployee {
    vehicle_count: number;
}

export interface ICustomerDetail extends ICustomer {
    vehicles: {
        id: string;
        name: string;
        plate_number: string;
        machine_frame: string;
        engine_volume: number;
        engine_type: string;
        type: string;
        production_year: number;
        brand: string;
    }[]
}

export interface IVehicle {
    name: string;
    plate_number: string;
    machine_frame: string;
    engine_volume: number;
    engine_type: string;
    type: string;
    production_year: number;
    brand: string;
    customer: string;
}
export interface IVehicleEdit {
    id: string;
    name: string;
    plate_number: string;
    machine_frame: string;
    engine_volume: number;
    engine_type: string;
    type: string;
    production_year: number;
    brand_id: string;
    customer_id: string;
}

export interface IPayment extends IBaseResponse {
    bank_name: string;
    account_name: string;
    account_number: string;
    tax: number;
}

export interface IUser extends IBaseResponse {
    username: string;
    role: string;
    gender: string;
    phone: string;
    whatsapp: string;
    enabled: boolean;
}

export interface IUserEdit {
    id: string;
    username: string;
    role: string;
    employee_id: string;
}

export interface ISetting {
    id: string;
    name: string;
    value: string;
    type: string;
    description: string;
}

export interface IService {
    id: string;
    service_code: string;
    vehicle_plate_number: string;
    customer_name: string;
    status: string;
    total: number;
    created_at: string;
}