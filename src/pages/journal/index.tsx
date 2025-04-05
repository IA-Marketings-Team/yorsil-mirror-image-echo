
import React from "react";
import { Navigate, Outlet, useLocation } from "react-router-dom";

const JournalPages: React.FC = () => {
  const location = useLocation();
  
  if (location.pathname === "/journal") {
    return <Navigate to="/journal/recharge-boutique" replace />;
  }
  
  return <Outlet />;
};

export default JournalPages;
