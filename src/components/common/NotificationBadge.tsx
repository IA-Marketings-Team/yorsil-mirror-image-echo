
import { useState, useEffect, useRef } from "react";
import { Bell } from "lucide-react";
import { cn } from "@/lib/utils";
import { formatDate } from "@/lib/utils";

interface Notification {
  id: number;
  title: string;
  message: string;
  createdAt: string;
  read: boolean;
  type: string;
}

interface NotificationBadgeProps {
  notifications: Notification[];
  onViewAll: () => void;
  onViewNotification: (id: number) => void;
  className?: string;
}

const NotificationBadge = ({
  notifications,
  onViewAll,
  onViewNotification,
  className
}: NotificationBadgeProps) => {
  const [isOpen, setIsOpen] = useState(false);
  const notificationRef = useRef<HTMLDivElement>(null);

  const unreadCount = notifications.filter(notif => !notif.read).length;

  const toggleDropdown = () => {
    setIsOpen(!isOpen);
  };

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (notificationRef.current && !notificationRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    };

    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, []);

  const getIcon = (type: string) => {
    // You could add more icons based on notification type
    return <Bell size={16} />;
  };

  return (
    <div ref={notificationRef} className={cn("relative", className)}>
      <button
        onClick={toggleDropdown}
        className="flex items-center justify-center h-10 w-10 rounded-full hover:bg-gray-100"
      >
        <Bell size={20} />
        {unreadCount > 0 && (
          <span className="absolute top-0 right-0 h-5 w-5 bg-red-500 text-white text-xs flex items-center justify-center rounded-full">
            {unreadCount > 9 ? "9+" : unreadCount}
          </span>
        )}
      </button>

      {isOpen && (
        <div className="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50">
          <div className="p-4 border-b border-gray-200">
            <div className="flex items-center justify-between">
              <h3 className="text-sm font-semibold">Notifications</h3>
              <span className="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                {unreadCount} non lues
              </span>
            </div>
          </div>

          <div className="max-h-72 overflow-y-auto">
            {notifications.length === 0 ? (
              <p className="p-4 text-sm text-gray-500 text-center">
                Aucune notification
              </p>
            ) : (
              notifications.slice(0, 5).map((notif) => (
                <button
                  key={notif.id}
                  onClick={() => {
                    onViewNotification(notif.id);
                    setIsOpen(false);
                  }}
                  className={cn(
                    "w-full text-left p-4 hover:bg-gray-50 flex gap-3 border-b border-gray-100",
                    !notif.read && "bg-blue-50"
                  )}
                >
                  <div className="flex-shrink-0 text-blue-500">
                    {getIcon(notif.type)}
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-gray-900 truncate">
                      {notif.title}
                    </p>
                    <p className="text-xs text-gray-500 truncate">
                      {notif.message.length > 50
                        ? `${notif.message.substring(0, 50)}...`
                        : notif.message}
                    </p>
                    <p className="text-xs text-gray-400 mt-1">
                      {formatDate(notif.createdAt)}
                    </p>
                  </div>
                  {!notif.read && (
                    <span className="flex-shrink-0 w-2 h-2 rounded-full bg-blue-500"></span>
                  )}
                </button>
              ))
            )}
          </div>

          <div className="p-4 border-t border-gray-200">
            <button
              onClick={() => {
                onViewAll();
                setIsOpen(false);
              }}
              className="w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium"
            >
              Voir toutes
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

export default NotificationBadge;
