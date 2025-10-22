import { createRouter, createWebHistory } from "vue-router";
import Categories from "./components/Categories/index.vue";
import Products from "./components/Products/index.vue";
import Brands from "./components/Brands/index.vue";

const routes = [
    {
        path: "/categories",
        component: Categories,
    },
    {
        path: "/brands",
        component: Brands,
    },
    {
        path: "/products",
        component: Products,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
