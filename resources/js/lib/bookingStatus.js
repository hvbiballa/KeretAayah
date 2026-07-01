const statusThemes = {
    confirmed: {
        badge: "bg-sky-50 text-sky-700",
        dot: "bg-sky-500",
    },
    ongoing: {
        badge: "bg-primary-50 text-primary-700",
        dot: "bg-primary-500",
    },
    pending: {
        badge: "bg-amber-50 text-amber-700",
        dot: "bg-amber-500",
    },
    completed: {
        badge: "bg-green-50 text-green-700",
        dot: "bg-green-500",
    },
    cancelled: {
        badge: "bg-red-50 text-red-700",
        dot: "bg-red-500",
    },
};

export const bookingStatusTheme = (status) =>
    statusThemes[status] || statusThemes.confirmed;

export const bookingStatusLabelKey = (status) =>
    `common.statuses.${status || "confirmed"}`;

export const bookingCanBeCancelled = (status) => status === "confirmed";
