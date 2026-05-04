import { useEffect, useState } from "react";
import axios from "axios";

import type {
    WilayahField,
    WilayahItem,
    WilayahLoading,
    WilayahSelected,
} from "@/types";

interface WilayahResponse {
    data: WilayahItem[];
}

const emptySelected: WilayahSelected = {
    province_id: "",
    regency_id: "",
    district_id: "",
    village_id: "",
};

const emptyLoading: WilayahLoading = {
    provinces: false,
    regencies: false,
    districts: false,
    villages: false,
};

export function useWilayah(initialValues: Partial<WilayahSelected> = {}) {
    const [provinces, setProvinces] = useState<WilayahItem[]>([]);
    const [regencies, setRegencies] = useState<WilayahItem[]>([]);
    const [districts, setDistricts] = useState<WilayahItem[]>([]);
    const [villages, setVillages] = useState<WilayahItem[]>([]);

    const [selected, setSelected] = useState<WilayahSelected>({
        ...emptySelected,
        ...initialValues,
    });

    const [loading, setLoading] = useState<WilayahLoading>(emptyLoading);

    useEffect(() => {
        setLoading((prev) => ({ ...prev, provinces: true }));

        axios
            .get<WilayahResponse>("/api/v1/wilayah/provinces")
            .then((res) => setProvinces(res.data.data))
            .finally(() =>
                setLoading((prev) => ({ ...prev, provinces: false })),
            );
    }, []);

    useEffect(() => {
        if (!selected.province_id) {
            setRegencies([]);
            setDistricts([]);
            setVillages([]);
            return;
        }

        setLoading((prev) => ({ ...prev, regencies: true }));

        axios
            .get<WilayahResponse>(
                `/api/v1/wilayah/regencies/${selected.province_id}`,
            )
            .then((res) => setRegencies(res.data.data))
            .finally(() =>
                setLoading((prev) => ({ ...prev, regencies: false })),
            );
    }, [selected.province_id]);

    useEffect(() => {
        if (!selected.regency_id) {
            setDistricts([]);
            setVillages([]);
            return;
        }

        setLoading((prev) => ({ ...prev, districts: true }));

        axios
            .get<WilayahResponse>(
                `/api/v1/wilayah/districts/${selected.regency_id}`,
            )
            .then((res) => setDistricts(res.data.data))
            .finally(() =>
                setLoading((prev) => ({ ...prev, districts: false })),
            );
    }, [selected.regency_id]);

    useEffect(() => {
        if (!selected.district_id) {
            setVillages([]);
            return;
        }

        setLoading((prev) => ({ ...prev, villages: true }));

        axios
            .get<WilayahResponse>(
                `/api/v1/wilayah/villages/${selected.district_id}`,
            )
            .then((res) => setVillages(res.data.data))
            .finally(() =>
                setLoading((prev) => ({ ...prev, villages: false })),
            );
    }, [selected.district_id]);

    function onChange(field: WilayahField, value: string) {
        setSelected((prev) => {
            const reset: Partial<WilayahSelected> = {};

            if (field === "province_id") {
                reset.regency_id = "";
                reset.district_id = "";
                reset.village_id = "";
            }

            if (field === "regency_id") {
                reset.district_id = "";
                reset.village_id = "";
            }

            if (field === "district_id") {
                reset.village_id = "";
            }

            return { ...prev, ...reset, [field]: value };
        });
    }

    return {
        provinces,
        regencies,
        districts,
        villages,
        selected,
        loading,
        onChange,
    };
}
