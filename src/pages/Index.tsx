
import React from "react";
import { useNavigate } from "react-router-dom";
import MainLayout from "@/components/layout/MainLayout";

const Index = () => {
  const navigate = useNavigate();
  
  // Redirect to the dashboard on component mount
  React.useEffect(() => {
    navigate("/journal/recharge-boutique");
  }, [navigate]);

  return <MainLayout>Redirection...</MainLayout>;
};

export default Index;
