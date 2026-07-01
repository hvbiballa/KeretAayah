export const rentalMethodLabel = (method, t = null) =>
    t ? t(`booking.methods.${method}`) : method === "hourly" ? "Hourly Rental" : "Daily Rental";

export const rentalMethodTheme = (method = "hourly") =>
    method === "daily"
        ? {
              pageBackground:
                  "bg-[radial-gradient(circle_at_top,_rgba(249,115,22,0.18),_transparent_38%),linear-gradient(180deg,#fff6ef_0%,#ffffff_36%)]",
              card: "bg-[linear-gradient(180deg,#fff8f3_0%,#ffffff_100%)]",
              border: "border-orange-200",
              surface: "bg-orange-50/70",
              surfaceStrong: "bg-orange-100/60",
              toggle: "bg-orange-100/70",
              text: "text-orange-700",
              textSoft: "text-orange-500",
              badge: "bg-orange-100 text-orange-800",
              badgeSoft: "bg-orange-50 text-orange-700",
              badgeStrong: "bg-orange-600 text-white",
              progressCurrent: "bg-orange-600 text-white",
              progressDone: "bg-orange-100 text-orange-700",
              progressPending: "bg-white text-orange-300",
              progressBar: "bg-orange-300",
              progressText: "text-orange-700",
              progressTextMuted: "text-orange-400",
              input:
                  "border-orange-200 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500",
              panel: "bg-linear-to-br from-red-900 via-orange-800 to-orange-700 text-white",
              panelSubtext: "text-orange-200",
              solidButton:
                  "bg-orange-600 text-white hover:bg-orange-700",
              lightButton:
                  "bg-white text-orange-700 hover:bg-orange-100",
              link: "text-orange-600 hover:text-orange-700",
              mutedHover: "hover:text-orange-700",
              fileInput:
                  "file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100",
          }
        : {
              pageBackground:
                  "bg-[radial-gradient(circle_at_top,_rgba(239,68,68,0.14),_transparent_36%),linear-gradient(180deg,#fff9f7_0%,#ffffff_34%)]",
              card: "bg-white",
              border: "border-primary-100",
              surface: "bg-primary-50/60",
              surfaceStrong: "bg-primary-50/80",
              toggle: "bg-primary-50",
              text: "text-primary-500",
              textSoft: "text-primary-400",
              badge: "bg-primary-100 text-primary-700",
              badgeSoft: "bg-primary-50 text-primary-700",
              badgeStrong: "bg-primary-600 text-white",
              progressCurrent: "bg-primary-600 text-white",
              progressDone: "bg-primary-100 text-primary-700",
              progressPending: "bg-white text-primary-400",
              progressBar: "bg-primary-300",
              progressText: "text-primary-600",
              progressTextMuted: "text-primary-400",
              input:
                  "border-primary-200 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500",
              panel: "bg-linear-to-br from-primary-900 via-primary-800 to-primary-700 text-white",
              panelSubtext: "text-primary-200",
              solidButton:
                  "bg-primary-600 text-white hover:bg-primary-700",
              lightButton:
                  "bg-white text-primary-700 hover:bg-primary-100",
              link: "text-primary-600 hover:text-primary-700",
              mutedHover: "hover:text-primary-700",
              fileInput:
                  "file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100",
          };

export const rentalUnitLabel = (method, duration, t = null) => {
    const singular = Number(duration) === 1;
    const unit = method === "hourly"
        ? singular ? "hour" : "hours"
        : singular ? "day" : "days";
    const label = t ? t(`booking.units.${unit}`) : unit;

    return `${Number(duration)} ${label}`;
};

const dateLocale = (locale = "ms") => (locale === "ms" ? "ms-MY" : "en-MY");

export const formatRentalDate = (value, locale = "ms") =>
    new Intl.DateTimeFormat(dateLocale(locale), {
        year: "numeric",
        month: "short",
        day: "numeric",
        timeZone: "Asia/Kuala_Lumpur",
    }).format(new Date(value));

export const formatRentalDateTime = (value, locale = "ms") =>
    new Intl.DateTimeFormat(dateLocale(locale), {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "numeric",
        minute: "2-digit",
        timeZone: "Asia/Kuala_Lumpur",
    }).format(new Date(value));

export const rentalPeriod = (rental, locale = "ms") =>
    `${formatRentalDateTime(rental.pickup_at, locale)} - ${formatRentalDateTime(rental.return_at, locale)}`;
