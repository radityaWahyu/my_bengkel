export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
}

export interface Admin {
    id: string;
    level: string;
    name: string;
    username: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        admin: Admin;
        user: User;
    };
};
