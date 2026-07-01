export const AVAILABLE_STATUS = "Available";

export const formatMYR = (value) => {
    const amount = Number(value || 0);

    return `RM ${amount.toFixed(2)}`;
};

export const isCarAvailable = (car) => car.status === AVAILABLE_STATUS;

export const carStatusLabel = (status, t = null) => {
    const key = {
        Available: "common.statuses.available",
        "Under Maintenance": "common.statuses.under_maintenance",
    }[status];

    return key && t ? t(key) : status;
};

export const getPrimaryCarImage = (car) =>
    car.images?.[0]?.path || "placeholder.png";

export const carImageUrl = (car) => {
    const path = getPrimaryCarImage(car);

    return path === "placeholder.png" ? "/placeholder.png" : `/storage/${path}`;
};

export const carImageAlt = (car, t = null) => {
    const name = car?.name || "Car";
    const brand = car?.brand || "";
    const model = car?.model || "";

    if (t) {
        return t("booking.car_image_alt", { name, brand, model });
    }

    return `${name} ${brand} ${model}`.trim();
};

export const imageUrl = (path) =>
    path === "placeholder.png" ? "/placeholder.png" : `/storage/${path}`;
