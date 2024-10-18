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
    id: string;
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
    employee_id: string;
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

export interface IServiceProduct {
    id: string;
    name: string;
    qty: number;
    price: number;
    total: number;
}

export interface IServiceRepair {
    id: string;
    name: string;
    employee_id: string | null;
    employee_name: string | null;
    qty: number;
    price: number;
    total: number;
    started_at: string;
    finished_at: string;
}

export interface IService {
    id: string;
    service_code: string;
    vehicle_plate_number: string;
    vehicle_name: string;
    customer_name: string;
    status: string;
    total: number;
    extra_pay: number;
    created_at: string;
}

export interface IServiceDetail {
    id: string;
    service_code: string;
    vehicle: IVehicle;
    status: string;
    total: number;
    payment_type: string;
    extra_pay: number;
    paid: number;
    created_at: string;
    description: string;
    notes: string;
    products: IServiceProduct[];
    repairs: IServiceRepair[];
}

export interface ISettingData {
    nama_bengkel: string;
    alamat_bengkel: string;
    logo_bengkel: string;
    nama_kontak: string;
    telepon_kontak: string;
    whatsapp_kontak: string;
}

export interface ICustomerPay {
    payment_id: string;
    extra_pay: number;
    paid: number;
    total: number;
    total_payment: number;
    payment_charge: number;
}

export interface ISale {
    id: string;
    sale_code: string;
    payment_type: string;
    product_count: number;
    status: string;
    total: number;
    extra_pay: number;
    created_at: string;
    employee: string;
}

export interface ISaleDetail {
    id: string;
    sale_code: string;
    status: string;
    total: number;
    payment_type: string;
    extra_pay: number;
    paid: number;
    created_at: string;
    products: ITransactionProduct[];
}

export interface ITransactionProduct {
    id: string;
    name: string;
    qty: number;
    price: number;
    total: number;
}

export interface IPurchase {
    id: string;
    supplier: string;
    invoice_number: string;
    purchase_code: string;
    payment_type: string;
    product_count: number;
    status: string;
    total: number;
    extra_pay: number;
    created_at: string;
    transaction_date: string;
    employee: string;
}

export interface IPurchaseProduct extends ITransactionProduct {
    old_price: number;
}

export interface IPurchaseDetail {
    id: string;
    purchase_code: string;
    invoice_number: string;
    supplier: ISupplier;
    status: string;
    total: number;
    payment_type: string;
    extra_pay: number;
    paid: number;
    transaction_date: string;
    created_at: string;
    purchase_products: IPurchaseProduct[];
    user: IUser;
}



