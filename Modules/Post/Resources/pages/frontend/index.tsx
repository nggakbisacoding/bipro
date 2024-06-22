import { Head } from "@inertiajs/react";
import { PageProps } from "@/types";

export default function Post({ auth }: PageProps) {
    return (
        <>
            <Head title="Post" />

            <span>Post User</span>
        </>
    );
}
