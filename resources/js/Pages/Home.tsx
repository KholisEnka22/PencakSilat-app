import { Head } from "@inertiajs/react";
import { useState } from "react";

import {
    Alert,
    Badge,
    Button,
    ConfirmModal,
    EmptyState,
    Input,
    Modal,
    Pagination,
    SearchableSelect,
    Tabs,
    Textarea,
    ToggleSwitch,
    type SelectOption,
    type TabItem,
} from "@/Components/ui";
import { APP_NAME } from "@/Utils/constants";
import AppLayout from "./Layouts/AppLayout";

type DashboardTab = "aktif" | "draft" | "arsip";

const perguruanOptions: SelectOption[] = [
    {
        value: "tapak-suci",
        label: "Tapak Suci",
        description: "Yogyakarta",
    },
    {
        value: "psht",
        label: "Persaudaraan Setia Hati Terate",
        description: "Madiun",
    },
    {
        value: "perisai-diri",
        label: "Perisai Diri",
        description: "Surabaya",
    },
    {
        value: "pagar-nusa",
        label: "Pagar Nusa",
        description: "Jombang",
    },
];

const wilayahOptions: SelectOption[] = [
    { value: "jakarta", label: "DKI Jakarta", description: "Provinsi" },
    { value: "jawa-barat", label: "Jawa Barat", description: "Provinsi" },
    { value: "jawa-tengah", label: "Jawa Tengah", description: "Provinsi" },
    { value: "jawa-timur", label: "Jawa Timur", description: "Provinsi" },
];

const tabItems: TabItem<DashboardTab>[] = [
    { value: "aktif", label: "Aktif", count: 24 },
    { value: "draft", label: "Draft", count: 5 },
    { value: "arsip", label: "Arsip", count: 12 },
];

export default function Home() {
    const [currentTab, setCurrentTab] = useState<DashboardTab>("aktif");
    const [selectedPerguruan, setSelectedPerguruan] = useState<string | null>(
        "tapak-suci",
    );
    const [selectedWilayah, setSelectedWilayah] = useState<string | null>(
        "jawa-timur",
    );
    const [isActive, setIsActive] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isConfirmOpen, setIsConfirmOpen] = useState(false);
    const [page, setPage] = useState(2);

    return (
        <AppLayout>
            <Head title="Dashboard" />

            <main className="min-h-screen bg-background text-foreground">
                <section className="app-container py-8">
                    <header className="flex flex-col gap-4 border-b border-border pb-6 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <Badge variant="primary">{APP_NAME}</Badge>
                            <h1 className="mt-3 text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
                                Dashboard
                            </h1>
                            <p className="mt-2 max-w-2xl text-sm leading-6 text-foreground/65">
                                Kelola data perguruan, rayon, pelatih, dan
                                anggota dari satu tampilan kerja.
                            </p>
                        </div>

                        <div className="flex flex-col gap-2 sm:flex-row">
                            <Button
                                type="button"
                                variant="secondary"
                                onClick={() => setIsConfirmOpen(true)}
                            >
                                Arsipkan
                            </Button>
                            <Button
                                type="button"
                                onClick={() => setIsModalOpen(true)}
                            >
                                Tambah Perguruan
                            </Button>
                        </div>
                    </header>

                    <div className="mt-6 grid gap-4 md:grid-cols-3">
                        {[
                            ["Perguruan", "24", "4 menunggu verifikasi"],
                            ["Rayon", "180", "Sebaran aktif nasional"],
                            ["Anggota", "1.248", "86 pendaftaran baru"],
                        ].map(([label, value, note]) => (
                            <div
                                key={label}
                                className="rounded-lg border border-border bg-surface p-5 shadow-soft dark:shadow-soft-dark"
                            >
                                <p className="text-sm font-medium text-foreground/60">
                                    {label}
                                </p>
                                <p className="mt-2 text-3xl font-bold text-foreground">
                                    {value}
                                </p>
                                <p className="mt-1 text-xs leading-5 text-foreground/55">
                                    {note}
                                </p>
                            </div>
                        ))}
                    </div>

                    <div className="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
                        <section className="space-y-6">
                            <div className="rounded-lg border border-border bg-surface p-5 shadow-soft dark:shadow-soft-dark">
                                <div className="flex flex-col gap-1">
                                    <h2 className="text-base font-semibold text-foreground">
                                        Form Perguruan
                                    </h2>
                                    <p className="text-sm leading-6 text-foreground/60">
                                        Data utama untuk proses administrasi
                                        perguruan.
                                    </p>
                                </div>

                                <div className="mt-5 grid gap-4 md:grid-cols-2">
                                    <Input
                                        label="Nama Perguruan"
                                        placeholder="Masukkan nama perguruan"
                                        defaultValue="Tapak Suci"
                                    />
                                    <SearchableSelect
                                        label="Perguruan Induk"
                                        value={selectedPerguruan}
                                        onChange={setSelectedPerguruan}
                                        options={perguruanOptions}
                                        helperText="Ketik nama atau wilayah."
                                    />
                                    <SearchableSelect
                                        label="Wilayah"
                                        value={selectedWilayah}
                                        onChange={setSelectedWilayah}
                                        options={wilayahOptions}
                                    />
                                    <Input
                                        label="Email Admin"
                                        type="email"
                                        placeholder="admin@perguruan.id"
                                    />
                                    <Textarea
                                        wrapperClassName="md:col-span-2"
                                        label="Catatan"
                                        placeholder="Catatan internal"
                                    />
                                    <ToggleSwitch
                                        className="md:col-span-2"
                                        checked={isActive}
                                        onChange={(event) =>
                                            setIsActive(event.target.checked)
                                        }
                                        label="Status aktif"
                                        description="Perguruan aktif dapat mengelola rayon dan anggota."
                                    />
                                </div>
                            </div>

                            <div className="rounded-lg border border-border bg-surface p-5 shadow-soft dark:shadow-soft-dark">
                                <Tabs
                                    items={tabItems}
                                    value={currentTab}
                                    onChange={setCurrentTab}
                                >
                                    <div className="overflow-hidden rounded-lg border border-border">
                                        <table className="w-full min-w-[640px] text-left text-sm">
                                            <thead className="bg-surface-muted text-xs uppercase text-foreground/55">
                                                <tr>
                                                    <th className="px-4 py-3 font-semibold">
                                                        Perguruan
                                                    </th>
                                                    <th className="px-4 py-3 font-semibold">
                                                        Wilayah
                                                    </th>
                                                    <th className="px-4 py-3 font-semibold">
                                                        Status
                                                    </th>
                                                    <th className="px-4 py-3 text-right font-semibold">
                                                        Anggota
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-border">
                                                {perguruanOptions
                                                    .slice(0, 3)
                                                    .map((perguruan, index) => (
                                                        <tr key={perguruan.value}>
                                                            <td className="px-4 py-3 font-medium text-foreground">
                                                                {perguruan.label}
                                                            </td>
                                                            <td className="px-4 py-3 text-foreground/65">
                                                                {
                                                                    perguruan.description
                                                                }
                                                            </td>
                                                            <td className="px-4 py-3">
                                                                <Badge
                                                                    variant={
                                                                        index ===
                                                                        1
                                                                            ? "warning"
                                                                            : "success"
                                                                    }
                                                                >
                                                                    {index === 1
                                                                        ? "Review"
                                                                        : "Aktif"}
                                                                </Badge>
                                                            </td>
                                                            <td className="px-4 py-3 text-right font-semibold text-foreground">
                                                                {320 + index * 84}
                                                            </td>
                                                        </tr>
                                                    ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </Tabs>

                                <Pagination
                                    className="mt-4"
                                    page={page}
                                    totalPages={8}
                                    onPageChange={setPage}
                                />
                            </div>
                        </section>

                        <aside className="space-y-6">
                            <Alert title="Verifikasi berjalan" variant="success">
                                18 data anggota sudah sinkron dengan data rayon
                                aktif.
                            </Alert>

                            <div className="rounded-lg border border-border bg-surface p-5 shadow-soft dark:shadow-soft-dark">
                                <h2 className="text-base font-semibold text-foreground">
                                    Ringkasan
                                </h2>
                                <dl className="mt-4 space-y-4">
                                    {[
                                        ["Tab aktif", currentTab],
                                        [
                                            "Pilihan",
                                            selectedPerguruan ?? "Belum dipilih",
                                        ],
                                        ["Halaman", String(page)],
                                    ].map(([label, value]) => (
                                        <div
                                            key={label}
                                            className="flex items-center justify-between gap-4"
                                        >
                                            <dt className="text-sm text-foreground/60">
                                                {label}
                                            </dt>
                                            <dd className="text-sm font-semibold text-foreground">
                                                {value}
                                            </dd>
                                        </div>
                                    ))}
                                </dl>
                            </div>

                            <EmptyState
                                title="Belum ada pengajuan"
                                description="Pengajuan baru akan muncul setelah admin perguruan mengirim data."
                                action={
                                    <Button
                                        type="button"
                                        variant="secondary"
                                        size="sm"
                                    >
                                        Muat Ulang
                                    </Button>
                                }
                            />
                        </aside>
                    </div>
                </section>

                <Modal
                    open={isModalOpen}
                    onClose={() => setIsModalOpen(false)}
                    title="Tambah Perguruan"
                    description="Lengkapi identitas awal sebelum data disimpan."
                    footer={
                        <>
                            <Button
                                type="button"
                                variant="secondary"
                                onClick={() => setIsModalOpen(false)}
                            >
                                Batal
                            </Button>
                            <Button
                                type="button"
                                onClick={() => setIsModalOpen(false)}
                            >
                                Simpan
                            </Button>
                        </>
                    }
                >
                    <div className="grid gap-4">
                        <Input
                            label="Nama Perguruan"
                            placeholder="Contoh: Perisai Diri"
                        />
                        <SearchableSelect
                            label="Wilayah"
                            value={selectedWilayah}
                            onChange={setSelectedWilayah}
                            options={wilayahOptions}
                        />
                        <Textarea
                            label="Alamat Sekretariat"
                            placeholder="Alamat lengkap"
                        />
                    </div>
                </Modal>

                <ConfirmModal
                    open={isConfirmOpen}
                    onClose={() => setIsConfirmOpen(false)}
                    onConfirm={() => setIsConfirmOpen(false)}
                    title="Arsipkan data?"
                    description="Data yang diarsipkan tidak tampil di daftar aktif."
                    confirmText="Arsipkan"
                    variant="danger"
                />
            </main>
        </AppLayout>
    );
}
