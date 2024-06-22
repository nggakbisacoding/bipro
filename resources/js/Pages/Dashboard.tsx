import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import { PageProps } from "@/types";
import { Card } from "antd";

export default function Dashboard({ auth }: PageProps) {
    return (
        <>
            <Head title="Dashboard" />

            <Card>You're logged ina!</Card>
        </>
    );
}
