
import React, { ReactNode } from "react";
import Sidebar from "./Sidebar";
import { Bell } from "lucide-react";
import { Avatar } from "@/components/ui/avatar";

interface MainLayoutProps {
  children: ReactNode;
}

const MainLayout: React.FC<MainLayoutProps> = ({ children }) => {
  return (
    <div className="flex h-screen bg-gray-50">
      <Sidebar />
      <div className="flex-1 flex flex-col overflow-hidden">
        <header className="bg-white border-b flex items-center justify-between py-4 px-6">
          <button className="text-gray-500 focus:outline-none">
            <svg className="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M4 6H20M4 12H20M4 18H11"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
            </svg>
          </button>
          
          <div className="flex items-center">
            <div className="relative">
              <button className="flex text-gray-500 focus:outline-none">
                <Bell size={20} />
                <span className="absolute top-0 right-0 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-xs text-white">
                  2
                </span>
              </button>
            </div>
            
            <div className="ml-4 flex items-center">
              <span className="text-gray-700 mr-2">Tarik</span>
              <Avatar className="h-8 w-8 bg-blue-500">
                <div className="flex h-full items-center justify-center text-white">T</div>
              </Avatar>
            </div>
          </div>
        </header>
        
        <main className="flex-1 overflow-y-auto bg-gray-50">
          {children}
        </main>
      </div>
    </div>
  );
};

export default MainLayout;
