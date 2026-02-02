"use client";

import React, { useEffect, useState } from 'react';
import Link from 'next/link';
import { API_BASE } from '@/lib/config';
import Button from '@/components/ui/Button';
import Card from '@/components/ui/Card';
import { Plus, Filter } from 'lucide-react';

// Define Order Interface type
interface Order {
  order_id: number;
  first_name: string;
  last_name: string;
  cleaning_type: string;
  status: string;
  service_date: string | null;
}

export default function Home() {
  const [orders, setOrders] = useState<Order[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState('');

  // ----------------------------------------------------------------
  // Effect: Fetch orders whenever the filter changes
  // ----------------------------------------------------------------
  useEffect(() => {
    fetchOrders();
  }, [filter]);

  // ----------------------------------------------------------------
  // Function: Fetch orders from PHP Backend
  // ----------------------------------------------------------------
  const fetchOrders = async () => {
    setLoading(true);
    try {
      // Build URL based on filter selection
      const url = filter
        ? `${API_BASE}/orders.php?status=${filter}`
        : `${API_BASE}/orders.php`;

      const res = await fetch(url);
      const data = await res.json();
      setOrders(data);
    } catch (error) {
      console.error("Failed to fetch orders:", error);
    } finally {
      setLoading(false);
    }
  };

  // Status Badge Color Mapping
  const statusColors: Record<string, string> = {
    New: "bg-blue-100 text-blue-800",
    Scheduled: "bg-yellow-100 text-yellow-800",
    Completed: "bg-green-100 text-green-800",
    Invoiced: "bg-purple-100 text-purple-800",
    Cancelled: "bg-red-100 text-red-800",
  };

  return (
    <div className="space-y-6">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-2xl font-bold text-brand-primary">Service Orders</h1>
          <p className="text-gray-500">Manage and track cleaning requests.</p>
        </div>
        <Link href="/create">
          <Button>
            <Plus size={18} /> New Order
          </Button>
        </Link>
      </div>

      <Card className="p-4">
        {/* Filter Controls */}
        <div className="flex items-center gap-2 mb-4">
          <Filter size={16} className="text-gray-500" />
          <span className="text-sm font-medium text-gray-700">Filter by Status:</span>
          <select
            className="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 p-1 bg-white border"
            value={filter}
            onChange={(e) => setFilter(e.target.value)}
          >
            <option value="">All Statuses</option>
            <option value="New">New</option>
            <option value="Scheduled">Scheduled</option>
            <option value="Completed">Completed</option>
            <option value="Invoiced">Invoiced</option>
          </select>
        </div>

        {/* Orders Table */}
        {loading ? (
          <div className="text-center py-10 text-gray-500">Loading orders...</div>
        ) : orders.length === 0 ? (
          <div className="text-center py-10 text-gray-500">No orders found.</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {orders.map((order) => (
                  <tr key={order.order_id} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{order.order_id}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{order.first_name} {order.last_name}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{order.cleaning_type}</td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusColors[order.status] || "bg-gray-100 text-gray-800"}`}>
                        {order.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{order.service_date || '-'}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <Link href={`/order/${order.order_id}`} className="text-brand-primary hover:text-gray-900 font-semibold">
                        View
                      </Link>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </Card>
    </div>
  );
}
