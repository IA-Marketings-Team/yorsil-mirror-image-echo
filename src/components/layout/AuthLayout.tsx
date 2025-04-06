
import { Outlet } from "react-router-dom";
import { useNavigate } from "react-router-dom";

const AuthLayout = () => {
  const navigate = useNavigate();

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
      <div className="sm:mx-auto sm:w-full sm:max-w-md">
        <img 
          src="/lovable-uploads/ae8aec2f-ef82-4168-932c-49424a936919.png" 
          alt="Yorsil Logo" 
          className="mx-auto h-12 w-auto"
        />
        <h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">Yorsil</h2>
      </div>
      <div className="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div className="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
          <Outlet />
        </div>
      </div>
    </div>
  );
};

export default AuthLayout;
