import mitt from 'mitt';

type ApplicationEvents = {
    'confirm:open': {
        title?: string;
        message: string;
        confirmText?: string;
        cancelText?: string;
        __resolve: (result: boolean) => void;
    };
    'toast:show': {
        message: string;
        style?: 'success' | 'danger' | 'warning' | 'info';
    };
};

export const eventBus = mitt<ApplicationEvents>();
