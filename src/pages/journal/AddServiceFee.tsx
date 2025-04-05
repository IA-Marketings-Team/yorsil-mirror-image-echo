
import React from "react";
import MainLayout from "@/components/layout/MainLayout";
import PageHeader from "@/components/common/PageHeader";
import { Button } from "@/components/ui/button";
import SelectInput from "@/components/common/SelectInput";
import { Input } from "@/components/ui/input";

const AddServiceFee: React.FC = () => {
  const boutiques = [
    { label: "Choisissez une boutique", value: "" },
    { label: "NEO SIV", value: "neo_siv" },
    { label: "2HML", value: "2hml" },
    { label: "cyber barriol", value: "cyber_barriol" },
    { label: "TECHNO NET", value: "techno_net" },
    { label: "IMS-TEXTILE", value: "ims_textile" },
  ];

  const services = [
    { label: "Choisissez un service", value: "" },
    { label: "FlixBus", value: "flixbus" },
    { label: "Ding", value: "ding" },
    { label: "Reloadly", value: "reloadly" },
    { label: "Aleda", value: "aleda" },
    { label: "DiaspoTransfert", value: "diaspo_transfert" },
  ];

  return (
    <MainLayout>
      <div className="p-6">
        <PageHeader
          title="Frais de services personnalisé"
          breadcrumbs={[
            { label: "Frais de services personnalisés", href: "/frais-de-services" },
            { label: "Liste", href: "/frais-de-services" },
            { label: "Ajout" },
          ]}
        />

        <div className="bg-white p-6 rounded-lg shadow">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div>
              <label className="block text-gray-700 mb-1">Boutique</label>
              <SelectInput
                options={boutiques}
                placeholder="Choisissez une boutique"
              />
            </div>

            <div>
              <label className="block text-gray-700 mb-1">Services</label>
              <SelectInput
                options={services}
                placeholder="Choisissez un service"
              />
            </div>

            <div>
              <label className="block text-gray-700 mb-1">Pourcentage</label>
              <Input
                type="text"
                placeholder="Entrez le pourcentage"
                className="w-full"
              />
            </div>
          </div>

          <div className="flex justify-end space-x-4">
            <Button 
              className="bg-teal-600 hover:bg-teal-700 text-white"
            >
              Ajouter
            </Button>
            <Button 
              variant="destructive"
            >
              Annuler
            </Button>
          </div>
        </div>
      </div>
    </MainLayout>
  );
};

export default AddServiceFee;
