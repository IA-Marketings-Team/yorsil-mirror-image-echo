
import React from "react";
import { Link } from "react-router-dom";
import { ChevronRight } from "lucide-react";

interface Breadcrumb {
  label: string;
  href?: string;
}

interface PageHeaderProps {
  title: string;
  breadcrumbs?: Breadcrumb[];
}

const PageHeader: React.FC<PageHeaderProps> = ({ title, breadcrumbs = [] }) => {
  return (
    <div className="bg-blue-50 p-6 rounded-lg mb-6">
      <h1 className="text-2xl font-bold text-gray-800">{title}</h1>
      {breadcrumbs.length > 0 && (
        <div className="flex items-center text-sm mt-2">
          {breadcrumbs.map((item, index) => (
            <React.Fragment key={index}>
              {item.href ? (
                <Link to={item.href} className="text-gray-600 hover:text-blue-600">
                  {item.label}
                </Link>
              ) : (
                <span className="text-gray-600">{item.label}</span>
              )}
              {index < breadcrumbs.length - 1 && (
                <ChevronRight size={14} className="mx-2 text-gray-400" />
              )}
            </React.Fragment>
          ))}
        </div>
      )}
    </div>
  );
};

export default PageHeader;
