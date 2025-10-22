import { createRouter, createWebHistory } from "vue-router";
import Categories from "./Components/Categories/Index.vue";
import Products from "./Components/Products/Index.vue";
import Brands from "./Components/Brands/Index.vue";

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
