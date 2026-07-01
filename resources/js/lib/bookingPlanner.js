const pad = (value) => String(value).padStart(2, "0");

export const parseDateTime = (date, time) => {
    if (!date || !time) return null;

    const value = new Date(`${date}T${time}:00`);

    return Number.isNaN(value.getTime()) ? null : value;
};

export const formatDateInput = (value) =>
    `${value.getFullYear()}-${pad(value.getMonth() + 1)}-${pad(value.getDate())}`;

export const formatTimeInput = (value) =>
    `${pad(value.getHours())}:${pad(value.getMinutes())}`;

export const roundToTwo = (value) =>
    Math.round((Number(value) + Number.EPSILON) * 100) / 100;

export const diffHours = (start, end) => {
    if (!start || !end || end <= start) return 0;

    return roundToTwo((end.getTime() - start.getTime()) / (1000 * 60 * 60));
};

export const diffCalendarDays = (start, end) => {
    if (!start || !end) return 0;

    const startDay = new Date(start.getFullYear(), start.getMonth(), start.getDate());
    const endDay = new Date(end.getFullYear(), end.getMonth(), end.getDate());

    return Math.round((endDay.getTime() - startDay.getTime()) / (1000 * 60 * 60 * 24));
};

export const buildReturnFromDuration = ({
    rental_method,
    pickup_date,
    pickup_time,
    duration_units,
}) => {
    const start = parseDateTime(pickup_date, pickup_time);
    const duration = Number(duration_units);

    if (!start || !duration || duration <= 0) {
        return {
            return_date: "",
            return_time: rental_method === "daily" ? pickup_time || "" : "",
        };
    }

    const end = new Date(start);

    if (rental_method === "hourly") {
        end.setTime(end.getTime() + duration * 60 * 60 * 1000);
    } else {
        end.setDate(end.getDate() + duration);
    }

    return {
        return_date: formatDateInput(end),
        return_time: rental_method === "daily" ? pickup_time : formatTimeInput(end),
    };
};

export const buildDurationFromReturn = ({
    rental_method,
    pickup_date,
    pickup_time,
    return_date,
    return_time,
}) => {
    const start = parseDateTime(pickup_date, pickup_time);
    const end = parseDateTime(
        return_date,
        rental_method === "daily" ? pickup_time : return_time,
    );

    if (!start || !end || end <= start) return 0;

    return rental_method === "hourly"
        ? diffHours(start, end)
        : diffCalendarDays(start, end);
};

export const isScheduleComplete = (schedule) =>
    Boolean(
        schedule?.pickup_date &&
            schedule?.pickup_time &&
            schedule?.return_date &&
            schedule?.return_time &&
            schedule?.rental_method,
    );
