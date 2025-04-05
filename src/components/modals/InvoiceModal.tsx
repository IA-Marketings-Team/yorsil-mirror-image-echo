
import { useState } from "react";
import { X } from "lucide-react";
import { Dialog } from "@/components/ui/dialog";
import { Select } from "@/components/ui/select";
import { Button } from "@/components/ui/button";

interface InvoiceModalProps {
  isOpen: boolean;
  onClose: () => void;
}

const InvoiceModal: React.FC<InvoiceModalProps> = ({ isOpen, onClose }) => {
  const [serviceType, setServiceType] = useState("ALI PHONE - ALI VOYAGE");
  const [taxType, setTaxType] = useState("HT");

  return (
    <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
      <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <div className="bg-white w-full max-w-md rounded-lg overflow-hidden">
          <div className="p-6">
            <div className="flex justify-between items-center mb-4">
              <h2 className="text-xl font-semibold">Facturation de services d'une boutique</h2>
              <button 
                onClick={onClose} 
                className="p-1 rounded-full hover:bg-gray-100"
              >
                <X size={20} />
              </button>
            </div>
            
            <div className="space-y-4">
              <div className="relative">
                <Select defaultValue={serviceType}>
                  <div className="border p-2 rounded-md flex justify-between">
                    {serviceType}
                    <span>▼</span>
                  </div>
                </Select>
              </div>
              
              <div className="relative">
                <Select defaultValue={taxType}>
                  <div className="border p-2 rounded-md flex justify-between">
                    {taxType}
                    <span>▼</span>
                  </div>
                </Select>
              </div>
              
              <Button 
                className="w-full bg-yorsil-accent hover:bg-yorsil-accent/90 text-white"
              >
                Export PDF
              </Button>
            </div>
          </div>
        </div>
      </div>
    </Dialog>
  );
};

export default InvoiceModal;
