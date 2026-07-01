import { router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

let sharedI18n = {};

const lookup = (translations, key) =>
    key.split(".").reduce((value, part) => value?.[part], translations);

const interpolate = (value, replacements = {}) =>
    Object.entries(replacements).reduce(
        (text, [key, replacement]) =>
            text.replaceAll(`:${key}`, String(replacement)),
        value,
    );

export const translateFrom = (i18n, key, replacements = {}) => {
    const value = lookup(i18n?.translations || {}, key);

    if (typeof value !== "string") {
        return key;
    }

    return interpolate(value, replacements);
};

export const installI18n = (app, initialPage) => {
    sharedI18n = initialPage?.props?.i18n || {};

    router.on("success", (event) => {
        sharedI18n = event.detail.page.props.i18n || sharedI18n;
    });

    app.config.globalProperties.$t = function (key, replacements = {}) {
        return translateFrom(this?.$page?.props?.i18n || sharedI18n, key, replacements);
    };
};

export const useI18n = () => {
    const page = usePage();
    const locale = computed(() => page.props.i18n?.locale || "ms");
    const supportedLocales = computed(
        () => page.props.i18n?.supportedLocales || ["ms", "en"],
    );

    const t = (key, replacements = {}) =>
        translateFrom(page.props.i18n, key, replacements);

    const switchLocale = (nextLocale) => {
        router.visit(route("language.switch", nextLocale), {
            method: "get",
            preserveScroll: true,
        });
    };

    return {
        locale,
        supportedLocales,
        t,
        switchLocale,
    };
};
