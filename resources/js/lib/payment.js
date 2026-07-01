export const paymentStatusLabel = (status, t = null) =>
    t ? t(`payment.statuses.${status}`) : ({
        pending: "Pending",
        proof_submitted: "Proof Submitted",
        partially_paid: "Partially Paid",
        paid: "Paid",
        refunded: "Refunded",
    })[status] ?? status;

export const paymentStatusClasses = (status) =>
    ({
        pending: "bg-amber-50 text-amber-700",
        proof_submitted: "bg-primary-50 text-primary-700",
        partially_paid: "bg-amber-50 text-amber-700",
        paid: "bg-green-50 text-green-700",
        refunded: "bg-primary-50 text-foreground",
    })[status] ?? "bg-primary-50 text-foreground";

export const paymentMethodLabel = (method, t = null) =>
    t ? (method ? t(`payment.methods.${method}`) : t("common.labels.not_set")) : ({
        cash: "Cash",
        bank_transfer: "Bank Transfer",
        duitnow_qr: "DuitNow QR",
        other: "Other",
    })[method] ?? "Not set";
