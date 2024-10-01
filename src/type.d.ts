export type OCSResponse<T> = {
    ocs: {
        data: T;
        meta: {
            status: string;
            statuscode: number;
            message?: string;
            totalitems?: string;
            itemsperpage?: string;
        };
    };
};

export type ServerConfigForm = {
    id: number;
    endpoint: string;
    username: string;
    password: string;
};

export type ServerConfig = {
    id: number;
    endpoint: string;
    username: string;
    health: number;
};

export type ServerUser = {
    uid: string;
    displayName: string;
    email: string | null;
};

export type UserResponse = {
    users: {
        id: string;
        displayname: string;
        email: string | null;
    }[];
};
